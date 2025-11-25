# System Users (sample data)

This document lists the sample users included in `database/sample_data.sql`. It is intended as a quick human-readable reference for developers exploring the system.

> Test password for all sample users: `password123` (the SQL file inserts hashed values). Do not use this in production.

| Username | Full Name | Email | Phone | Role | Active | Status |
|---|---|---|---|---|---|---|
| dean.petra | Dr. Ahmad Al-Masri | dean@uop.edu.jo | +962 6 5715546 | dean | TRUE | - |
| coordinator1 | Sara Al-Khalili | coord1@uop.edu.jo | +962 6 5715547 | coordinator | TRUE | - |
| supervisor1 | Dr. Mohammed Al-Hashimi | supervisor1@uop.edu.jo | +962 6 5715548 | supervisor | TRUE | - |
| supervisor2 | Dr. Fatima Al-Zahra | supervisor2@uop.edu.jo | +962 6 5715549 | supervisor | TRUE | - |
| supervisor3 | Dr. Khalid Al-Ahmad | supervisor3@uop.edu.jo | +962 6 5715550 | supervisor | TRUE | - |
| student001 | Omar Al-Mahmoud | student001@uop.edu.jo | +962 79 1234567 | student | TRUE | in_progress |
| student002 | Layla Al-Rashid | student002@uop.edu.jo | +962 79 2345678 | student | TRUE | in_progress |
| student003 | Yusuf Al-Hassan | student003@uop.edu.jo | +962 79 3456789 | student | TRUE | assigned |
| student004 | Mariam Al-Ibrahim | student004@uop.edu.jo | +962 79 4567890 | student | TRUE | completed |
| student005 | Hassan Al-Nasser | student005@uop.edu.jo | +962 79 5678901 | student | TRUE | assigned |

## Notes
- These entries come from `database/sample_data.sql` (look for the `INSERT INTO users` section).
- The password values in the SQL file are bcrypt hashes. The comment in the SQL file states the test password is `password123`.
- User IDs are assigned by the database on insert; refer to the `users` table contents in your local database to see `id` values.
- Students have separate detail records in the `students` table (see `database/sample_data.sql` section 2). Student accounts in `users` are separate from `students` rows linked by `training_assignments.student_id` (IDs shown in sample data).

## Quick lookup SQL
Run this in your MySQL client connected to the `field_training_db` database to get the same list from your live DB:

```sql
-- Get users with current training assignment status (if any)
SELECT u.id AS user_id,
	   u.username,
	   u.full_name,
	   u.email,
	   u.phone,
	   u.user_type,
	   u.is_active,
	   COALESCE(ta.status, 'no_assignment') AS assignment_status
FROM users u
LEFT JOIN training_assignments ta ON u.id = ta.student_id
ORDER BY u.user_type, u.username;
```

If you'd like, I can also add a small script or a CSV export file generated from your live database. Tell me if you want that next.
