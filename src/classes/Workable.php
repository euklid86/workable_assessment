<?php

/**
 * Class Workable
 * 
 * This class handles parsing CSV uploads and sending candidate data to the Workable API.
 * It supports job-specific candidate creation and posting to the general talent pool.
 */
class Workable
{
    /**
     * @var string Workable API Bearer token
     */
    protected $authorization = 'BmdybnXX37tMhGzcQkDSO4-YJG1RAIOuRanOjnAsqmk';

    /**
     * @var string Subdomain of the Workable account
     */
    protected $subdomain = 'assignmentcorporate';

    /**
     * @var int Delay in seconds between API requests (for rate-limiting)
     */
    protected $sleep_seconds = 5;

    /**
     * Public entry method to start processing the uploaded CSV.
     *
     * @param array $request Request parameters (e.g. $_POST data)
     * @return array Result of the sync operation
     */
    public function sync_file($request)
    {
        return $this->parse_csv($request);
    }

    /**
     * Parses a CSV file uploaded through a form, creates candidates, and posts to Workable.
     *
     * @param array $request Request parameters (e.g. $_POST)
     * @param string $inputName The name of the file input field (default: 'file')
     * @return array Result status and message
     */
    public function parse_csv($request, $inputName = 'file')
    {
        $this->log_message("Script started");

        // Ensure file is uploaded properly
        if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'File upload failed.'];
        }

        $fileTmpPath = $_FILES[$inputName]['tmp_name'];
        $fileType = mime_content_type($fileTmpPath);

        $allowedTypes = ['text/csv', 'text/plain', 'application/vnd.ms-excel'];
        if (!in_array($fileType, $allowedTypes)) {
            return ['success' => false, 'message' => 'Invalid file type. Please upload a CSV file.'];
        }

        if (($handle = fopen($fileTmpPath, 'r')) !== false) {
            $header = fgetcsv($handle);
            if ($header === false) {
                return ['success' => false, 'message' => 'CSV file is empty or invalid.'];
            }

            $this->log_message("Jobs fetching");
            $jobs = $this->fetch_jobs();

            if (isset($jobs['error'])) {
                $this->log_message("Jobs fetching failed: " . $jobs['error']);
                return ['fail' => true, 'message' => $jobs['error']];
            }

            $jobs_map = [];
            foreach ($jobs as $job) {
                $jobs_map[$job->title] = $job->shortcode;
            }

            // Iterate through CSV rows
            while (($data = fgetcsv($handle)) !== false) {
                if (count($data) === count($header)) {
                    $row_data = array_combine($header, $data);
                    $job_short_code = $jobs_map[$row_data['Position']] ?? null;

                    $candidate_data = $this->generate_payload($row_data);
                    $candidate_payload = json_encode($candidate_data);

                    $this->log_message("Post candidate payload: " . $candidate_payload);

                    $response = $this->post_candidate($candidate_payload, $job_short_code);
                    sleep($this->sleep_seconds);

                    if (isset($response->error)) {
                        $this->log_message("An error occurred: " . $response->error);
                    } else {
                        $this->log_message("Post candidate success: " . json_encode($response));

                        if (!empty($request['talentPool'])) {
                            $this->log_message("Post candidate to talent pool payload: " . $candidate_payload);
                            $response = $this->post_candidate_to_talent_pool($candidate_payload);

                            if (isset($response->error)) {
                                $this->log_message("Post to talent pool failed: " . $response->error);
                            } else {
                                $this->log_message("Post to talent pool success: " . json_encode($response));
                            }
                        }
                    }
                }
            }

            fclose($handle);
            $this->log_message("Script finished");
            return ['success' => true];
        }

        $this->log_message("Script finished. Failed to read the uploaded CSV file.");
        return ['success' => false, 'message' => 'Failed to read the uploaded CSV file.'];
    }

    /**
     * Creates a properly formatted candidate payload array for the Workable API.
     *
     * @param array $row_data Parsed row from CSV
     * @return array Formatted candidate data
     */
    private function generate_payload($row_data)
    {
        return [
            "sourced" => true,
            "candidate" => [
                "firstname" => $row_data['First Name'],
                "lastname" => $row_data['Last Name'],
                "headline" => "Professional Administration Manager",
                "address" => $row_data['Zip'] . ' ' . $row_data['Address'] . ' ' . $row_data['City'] . ' ' . $row_data['Country'],
                "phone" => $row_data['Phone'],
                "email" => 'emi_' . $row_data['Email'],
                "education_entries" => [
                    [
                        "degree" => $row_data['Education Level'],
                        "school" => $row_data['Education Institution Name']
                    ]
                ],
                "social_profiles" => [
                    [
                        "type" => "twitter",
                        "name" => "Twitter",
                        "username" => $row_data['Twitter Username'],
                        "url" => "http://www.twitter.com/" . substr($row_data['Twitter Username'], 1)
                    ],
                    [
                        "type" => "linkedin",
                        "name" => "LinkedIn",
                        "url" => $row_data['Linkedin Url']
                    ]
                ]
            ]
        ];
    }

    /**
     * Fetches all job postings from Workable for the configured subdomain.
     *
     * @return array List of jobs
     */
    public function fetch_jobs()
    {
        $url = "https://{$this->subdomain}.workable.com/spi/v3/jobs";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->authorization,
            'Content-Type: application/json'
        ]);

        $jobs_response = json_decode(curl_exec($curl));
        return $jobs_response->jobs ?? [];
    }

    /**
     * Sends a candidate to Workable either to a job-specific posting or to the talent pool.
     *
     * @param string $candidate_payload JSON-formatted candidate data
     * @param string|null $short_code Job shortcode; if null, candidate goes to talent pool
     * @return object API response
     */
    public function post_candidate($candidate_payload, $short_code = null)
    {
        $endpoint = $short_code
            ? "https://{$this->subdomain}.workable.com/spi/v3/jobs/{$short_code}/candidates"
            : "https://{$this->subdomain}.workable.com/spi/v3/jobs/talent_pool/candidates";

        $curl = curl_init($endpoint);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $candidate_payload);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->authorization,
            'Content-Type: application/json'
        ]);

        return json_decode(curl_exec($curl));
    }

    /**
     * Posts candidate directly to the talent pool without assigning a job.
     *
     * @param string $candidate_payload JSON-formatted candidate data
     * @return object API response
     */
    public function post_candidate_to_talent_pool($candidate_payload)
    {
        return $this->post_candidate($candidate_payload);
    }

    /**
     * Logs a message to the log file with a timestamp.
     *
     * @param string $message The message to log
     */
    public function log_message($message)
    {
        $logFile = '../logs/candidates_import.log';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
    }
}
