
# Log Analysis Commands

---

## 1) Most Frequent 404 Paths

```bash
grep "status=404" 2014-09-03.log | sed -n 's/.*path=\([^ ]*\).*/\1/p' | sort | uniq -c | sort -nr
```

**Explanation:**  
- `grep "status=404"` pulls out only the lines where a 404 error happened.
- `sed -n 's/.*path=\([^ ]*\).*/\1/p'` from each 404 error line, it extracts only the URL path that was not found.
- `sort` Sorts all the extracted paths alphabetically.
- `uniq -c` collapses identical consecutive lines and counts how many times each path appears.
- `sort -nr` sorts numerically.

---

## 2) Average Duration Calculation

```bash
awk -F'duration=' '{if (NF>1) sum += $2; count++} END {if (count > 0) print "Average duration:", sum/count}' 2014-09-03.log
```

**Explanation:**  
- `-F'duration='` — splits each line using `duration=` as the delimiter.  
- `$2` — extracts the part after `duration=`.  
- `sum += $2` — adds up all durations.  
- `count++` — counts lines that include a duration.  
- In the `END` block, prints the average duration.

---

## 3) Most Queried Tables in SELECT Statements

```bash
grep -i "select" 2014-09-03.log | \
grep -oiE 'from\s+"[^"]+"' | \
sed -E 's/.*"([^"]+)".*/\1/' | \
awk '{print tolower($0)}' | \
sort | uniq -c | sort -nr
```

**Explanation:**  
- `grep -i "select"` — finds SELECT queries.  
- `grep -oiE 'from\s+[a-zA-Z0-9_]+'` — extracts the table name after FROM.  
- `awk '{print tolower($0)}'` — normalizes table names to lowercase.  
- `sort | uniq -c | sort -nr` — counts and sorts by frequency.

---

## 4) Extract Redirect URIs

```bash
grep -E 'redirect_uri"=>"[^"]+"' 2014-09-03.log | sed -n 's/.*path=\([^ ]*\).*/\1/p'
```
**Explanation:**  
- `grep -E 'redirect_uri"=>'` extracts all lines that mention 'redirect_uri"=>'
- `sed -n 's/.*path=\([^ ]*\).*/\1/p'` extracts only the path field from those matching lines.

---

## 5) Frequent Backend Timeouts

```bash
grep "status=50" 2014-09-03.log | sed -n 's/.*path=\([^ ]*\).*/\1/p'
```
**Explanation:**  
- `grep "status=50"` picks out all log entries where the status code starts with 50, which usually indicates server errors (like 500, 502, etc.).
- `sed -n 's/.*path=\([^ ]*\).*/\1/p'` extracts just the path from each error line.

**Observation:**  
The path `backend/reports/detailed_export` seems to experience frequent timeouts.

---
