**3.2.1 UC-100: Authenticate User**

**3.2.1.1 Brief Description**

This use case allows a registered user (Administrator, HOD, PSM, Lecturer, Faculty Management) to log in to TFMS and access the role-based dashboard.

**3.2.1.2 Characteristic of Activation**

Activated when the user navigates to the TFMS login page.

**3.2.1.3 Pre-Condition**

The user has a valid TFMS account.

**3.2.1.4 Basic Flow**

1.  This use case begins when the user opens the TFMS login page. (SRS_REQ_101)

2.  TFMS displays the login form requesting username and password. **\[R1\]** (SRS_REQ_102)

3.  The user enters username and password and submits the login request. (SRS_REQ_103)

4.  TFMS authenticates the user credentials. (SRS_REQ_104)

5.  If authentication is successful, TFMS displays the appropriate role-based dashboard. **\[R4\]** (SRS_REQ_105)

6.  If authentication is not successful or special handling is required, TFMS follows one of the following options: (SRS_REQ_106)

<!-- -->

a.  **\[A1: Invalid Username or Password\]** (SRS_REQ_107)

b.  **\[A2: Account Locked\]** (SRS_REQ_108)

c.  **\[A3: First-Time Login Password Change\]** (SRS_REQ_109)

d.  **\[A4: Forgot Password\]** (SRS_REQ_110)

<!-- -->

7.  This use case ends.

**3.2.1.5 Alternative Flow**

**\[A1: Invalid Username or Password\]** (SRS_REQ_107)

1.  TFMS denies access and displays: "Invalid username or password." (SRS_REQ_107_1)

2.  TFMS counts the failed attempt and applies the lock policy when the limit is reached. **\[R2\]** (SRS_REQ_107_2)

3.  This alternative flow ends.

**\[A2: Account Locked\]** (SRS_REQ_108)

1.  TFMS denies access and displays: "Your account is locked. Please contact the Administrator." (SRS_REQ_108_1)

2.  This alternative flow ends.

**\[A3: First-Time Login Password Change\]** (SRS_REQ_109)

1.  TFMS requires the user to change password before dashboard access. **\[R3\], \[R1\]** (SRS_REQ_109_1)

2.  This alternative flow ends.

**\[A4: Forgot Password\]** (SRS_REQ_110)

1.  TFMS allows the user to request a password reset via registered email and displays: "A password reset link has been sent to your registered email." (SRS_REQ_110_1)

2.  This alternative flow ends.

**3.2.1.6 Exception Flow**

None.

**3.2.1.7 Post Condition(s)**

-   Successful login authenticates and grants access to the role-based dashboard.

-   Failed login denies access; locked accounts remain locked until unlocked by Administrator.

    8.  **Rule(s) and Constraint(s)**

<!-- -->

-   **\[R1: Password Policy\]** TFMS passwords shall comply with the defined password policy (minimum of 8 and maximum of 16 alphanumeric characters \[a-z | A-Z | 0-9\]).

-   **\[R2: Account Lock Policy\]** After the maximum allowed (3) failed login attempts, the account is locked and can only be unlocked by the Administrator.

-   **\[R3: First-Time Login Policy\]** On first-time login, the user must change password before accessing their dashboard

-   **\[R4: Audit Logging Rule\]** ﻿ TFMS records an audit log entry.

**3.2.1.9 GUI**

N/A

**3.2.2 UC-200 -- Manage Master Data**

**3.2.2.1 Brief Description**

This use case allows the Administrator to manage and maintain master data required for TFMS operation, including academic session/semester, workload threshold range, staff and taskforce records, unlocking user accounts, resetting passwords, and viewing audit logs.

**3.2.2.2 Characteristic of Activation**

Activated when the Administrator selects one of the available master data management options from the Administrator Dashboard.

**3.2.2.3 Pre-Condition**

Administrator is authenticated.

**3.2.2.4 Description**

**3.2.2.4.1 Basic Flow**

1.  This use case begins when the Administrator selects one of the following master data management options: (SRS_REQ_201)

<!-- -->

a.  **\[A1: Set Academic Session / Semester\]** (SRS_REQ_202)

b.  **\[A2: Configure Workload Threshold Range\]** (SRS_REQ_203)

c.  **\[A3: Maintain Staff Master Data\]** (SRS_REQ_204)

d.  **\[A4: Maintain Taskforce Master Data\]** (SRS_REQ_205)

e.  **\[A5: Unlock User Account\]** (SRS_REQ_206)

f.  **\[A6: Reset User Password\]** (SRS_REQ_207)

g.  **\[A7: View Audit Log\]** (SRS_REQ_208)

<!-- -->

2.  This use case ends.

**3.2.2.4.2 Alternative Flows**

**\[A1: Set Academic Session / Semester\]** (SRS_REQ_202)

1.  Administrator selects Academic Session / Semester. (SRS_REQ_202_1)

2.  Administrator updates academic session and semester values. (SRS_REQ_202_2)

3.  TFMS saves the updated values and displays "Academic session and semester updated successfully." **\[R4\]** (SRS_REQ_202_3)

4.  This alternative flow ends.

**\[A2: Configure Workload Threshold Range\]** (SRS_REQ_203)

1.  Administrator selects Workload Threshold Range. (SRS_REQ_203_1)

2.  Administrator updates minimum and maximum workload threshold values. **\[R5\]** (SRS_REQ_203_2)

3.  TFMS saves the updated threshold values and displays "Workload thresholds updated successfully**."** **\[R4\]** (SRS_REQ_203_3)

4.  This alternative flow ends.

**\[A3: Maintain Staff Master Data\]** (SRS_REQ_204)

1.  Administrator selects Staff Master Data. (SRS_REQ_204_1)

2.  Administrator registers a new staff member or updates existing staff details. **\[R6\]** (SRS_REQ_204_2)

3.  TFMS saves the staff data and displays "Staff master data updated successfully." **\[R4\]** (SRS_REQ_204_3)

4.  This alternative flow ends.

**\[A4: Maintain Taskforce Master Data\]** (SRS_REQ_205)

1.  Administrator selects Taskforce Master Data. (SRS_REQ_205_1)

2.  Administrator creates, updates, activates, or deactivates a taskforce. (SRS_REQ_205_2)

3.  TFMS saves the taskforce data and displays "Taskforce master data updated successfully." **\[R4\]** (SRS_REQ_205_3)

4.  This alternative flow ends.

**\[A5: Unlock User Account\]** (SRS_REQ_206)

1.  Administrator selects Unlock User Account. (SRS_REQ_206_1)

2.  Administrator selects a locked account and confirms unlock. (SRS_REQ_206_2)

3.  TFMS unlocks the account and displays "User account unlocked successfully." **\[R4\]** (SRS_REQ_206_3)

4.  This alternative flow ends.

**\[A6: Reset User Password\]** (SRS_REQ_207)

1.  Administrator selects Reset User Password. (SRS_REQ_207_1)

2.  Administrator selects a user account and confirms password reset. **\[R1\]** (SRS_REQ_207_2)

3.  TFMS resets the password and displays "Password reset successfully." **\[R4\]** (SRS_REQ_207_3)

4.  This alternative flow ends.

**\[A7: View Audit Log\]** (SRS_REQ_208)

1.  Administrator selects View Audit Log. (SRS_REQ_208_1)

2.  TFMS displays recorded audit log entries. (SRS_REQ_208_2)

3.  This alternative flow ends.

**3.2.2.4.3 Exception Flow**

None.

**3.2.2.4.4 Post-Condition(s)**

Master data updates are stored successfully. Administrative actions are recorded in the audit log. Updated values take effect immediately.

5.  **Rule(s) and Constraint(s)**

-   **\[R1: Password Policy\]** TFMS passwords shall comply with the defined password policy (minimum of 8 and maximum of 16 alphanumeric characters \[a-z | A-Z | 0-9\]).

-   **\[R4: Audit Logging Rule\]** TFMS records an audit log entry.

-   **\[R5: Workload Threshold Range\]** Minimum workload = 0, maximum workload = 20.

-   **\[R6: Staff ID Rule\]** Staff ID must be unique and autogenerated.

**3.2.2.4.6 GUI**

**3.2.3 UC-300 -- Manage Department Taskforce and Workload**

**3.2.3.1 Brief Description**

This use case enables the HOD to manage department taskforce and workload activities, including viewing department taskforces, managing taskforce membership, submitting membership update requests for PSM approval, and viewing department workload and fairness information.

**3.2.3.2 Characteristic of Activation**

Activated when the HOD selects one the Department Taskforce and Workload Manage options from the HOD Dashboard.

**3.2.3.3 Pre-Condition**

HOD is authenticated.

**3.2.3.4 Description**

**3.2.3.4.1 Basic Flow**

1.  This use case begins when the HOD selects one of the following taskforce and workload options: (SRS_REQ_301)

<!-- -->

a.  **\[A1: View Department Taskforces\]** (SRS_REQ_302)

b.  **\[A2: Manage Taskforce Membership\]** (SRS_REQ_303)

c.  **\[A3: View Department Workload and Fairness Summary\]** (SRS_REQ_304)

<!-- -->

2.  This use case ends.

**3.2.3.4.2 Alternative Flows**

**\[A1: View Department Taskforces\]** (SRS_REQ_302)

1.  The HOD selects view department taskforces. (SRS_REQ_302_1)

2.  TFMS displays the department taskforce list (name, category, leader/chair, duration, status). (SRS_REQ_302_2)

3.  TFMS displays "No taskforces defined for your department." if none exist. (SRS_REQ_302_3)

4.  This alternative flow ends.

**\[A2: Manage Taskforce Membership\]** (SRS_REQ_303)

1.  The HOD selects Manage Taskforce Membership. (SRS_REQ_303_1)

2.  TFMS displays department taskforces and current membership information. (SRS_REQ_303_2)

3.  TFMS displays system-generated suggestions for membership updates based on under-loaded lecturers. (SRS_REQ_303_3)

4.  HOD select to Add/Update/Remove staff member form the taskforce (SRS_REQ_303_4)

5.  TFMS records the request as Pending PSM Approval. **\[R4\]** (SRS_REQ_303_5)

6.  TFMS displays "Request submitted to PSM for approval**."** (SRS_REQ_303_6)

7.  This alternative flow ends.

**\[A3: View Department Workload and Fairness Summary\]** (SRS_REQ_304)

1.  The HOD selects View Department Workload and Fairness Summary. (SRS_REQ_304_1)

2.  TFMS displays department workload totals and workload status classifications (Underloaded/Balanced/Overloaded). **\[R7\]** (SRS_REQ_304_2)

3.  TFMS displays department-level fairness indicators/summary. (SRS_REQ_304_3)

4.  This alternative flow ends.

**3.2.3.4.3 Exception Flow**

None.

**3.2.3.4.4 Post-Condition(s)**

-   Membership update requests may be submitted for PSM approval.

-   Department workload and fairness information may be viewed.

**3.2.3.4.5 Rule(s) and Constraint(s)**

-   **\[R4: Audit Logging Rule\]** TFMS records an audit log entry.

-   **\[R7: Loaded Rule\]** Under-loaded \<= 5, Balanced \< 10, Over-loaded \> 10.

**3.2.3.4.6 GUI**

N/A

## 3.2.4 UC-400 -- Manage Faculty Taskforce and Workload

### 3.2.4.1 Brief Description

This use case enables the PSM to view faculty taskforces, review and decide on departmental submissions, apply exceptional modification after approval with justification, and generate/export faculty reports.

### 3.2.4.2 Characteristic of Activation

Activated when the PSM selects one of the Manage Faculty Taskforce and Workload option from the PSM Dashboard.

### 3.2.4.3 Pre-Condition

PSM is authenticated.

### 3.2.4.4 Description

#### 3.2.4.4.1 Basic Flow

1.  This use case begins when the PSM selects Manage Faculty Taskforce and Workload. (SRS_REQ_401)

<!-- -->

a.  **\[A1: View Faculty Taskforces\]** (SRS_REQ_402)

b.  **\[A2: Review Departmental Taskforce Submissions\]** (SRS_REQ_403)

c.  **\[A3: Exceptional Modification After Approval\]** (SRS_REQ_404)

d.  **\[A4: Generate Faculty Reports\]** (SRS_REQ_405)

<!-- -->

2.  This use case ends.

#### 3.2.4.4.2 Alternative Flows

**\[A1: View Faculty Taskforces\]** (SRS_REQ_402)

1.  The PSM selects View Faculty Taskforces. (SRS_REQ_402_1)

2.  TFMS displays taskforces across departments (membership, category, dates, owner). (SRS_REQ_402_2)

3.  This alternative flow ends.

**\[A2: Review Departmental Taskforce Submissions\]** (SRS_REQ_403)

1.  The PSM selects Review Departmental Submissions. (SRS_REQ_403_1)

2.  TFMS displays pending submissions. (SRS_REQ_403_2)

3.  The PSM selects a submission to review. (SRS_REQ_403_3)

4.  TFMS displays submission details. (SRS_REQ_403_4)

5.  The PSM selects Approve or Reject. (SRS_REQ_403_5)

6.  TFMS applies the decision and lock the taskforce if approved. **\[R4\]**, **\[R8\]** (SRS_REQ_403_6)

7.  This alternative flow ends.

**\[A3: Exceptional Modification After Approval\]** (SRS_REQ_404)

1.  The PSM modify locked assignment with justification. (SRS_REQ_404_1)

2.  TFMS applies the modification. **\[R4\]** (SRS_REQ_404_2)

3.  This alternative flow ends.

**\[A4: Generate Faculty Reports\]** (SRS_REQ_405)

1.  The PSM selects Generate Faculty Reports. (SRS_REQ_405_1)

2.  TFMS provides report options (taskforce lists, workload summaries, under/overload). (SRS_REQ_405_2)

3.  The PSM selects report type and format (PDF/Excel). (SRS_REQ_405_3)

4.  TFMS generates and provides the report output. (SRS_REQ_405_4)

5.  This alternative flow ends.

### 3.2.4.4.3 Exception Flow

None.

### 3.2.4.4.4 Post-Condition(s)

Submissions may be approved/rejected; exceptional modifications may be applied; reports may be generated/exported.

### 3.2.4.4.5 Rule(s) and Constraint(s)

-   **\[R4: Audit Logging Rule\]** TFMS records an audit log entry.

-   **\[R8: Notification Rule\]** TFMS notifies relevant stakeholders (HOD and affected Lecturer) on approval/rejection and modification outcomes.

### 3.2.4.4.6 GUI

N/A

**3.2.5 UC-500 -- Manage Taskforce and Workload**

**3.2.5.1 Brief Description**

This use case allows the Lecturer to view assigned taskforces, view workload summary and classification based on workload thresholds, submit workload remarks to HOD for consideration, and view historical taskforce and workload records for past semesters.

**3.2.5.2 Characteristic of Activation**

Activated when the Lecturer selects one of the available taskforce and workload management options from the Lecturer Dashboard.

**3.2.5.3 Pre-Condition**

Lecturer is authenticated.

**3.2.5.4 Description**

**3.2.5.4.1 Basic Flow**

1.  This use case begins when the Lecturer selects one the following available taskforce and workload management options: (SRS_REQ_501)

<!-- -->

a.  **\[A1: View Assigned Taskforces\]** (SRS_REQ_502)

b.  **\[A2: View Workload Summary\]** (SRS_REQ_503)

c.  **\[A3: Submit Workload Remarks to HOD\]** (SRS_REQ_504)

d.  **\[A4: View Historical Taskforce and Workload Records\]** (SRS_REQ_505)

e.  **\[A5: Download / Print Workload Summary\]** (SRS_REQ_506)

<!-- -->

2.  This use case ends.

**3.2.5.4.2 Alternative Flows**

**\[A1: View Assigned Taskforces\]** (SRS_REQ_502)

1.  The Lecturer selects View Assigned Taskforces. (SRS_REQ_502_1)

2.  TFMS displays the Lecturer's current semester taskforce assignments (taskforce name, category, role). (SRS_REQ_502_2)

3.  TFMS displays "You are not currently assigned to any taskforce." if none exist. (SRS_REQ_502_3)

4.  This alternative flow ends.

**\[A2: View Workload Summary\]** (SRS_REQ_503)

1.  The Lecturer selects View Workload Summary. (SRS_REQ_503_1)

2.  TFMS calculates and displays total workload and status (Underloaded / Balanced / Overloaded), including breakdown by taskforce/category. **\[R7\]** (SRS_REQ_503_2)

3.  This alternative flow ends.

**\[A3: Submit Workload Remarks to HOD\]** (SRS_REQ_504)

1.  The Lecturer selects Submit Workload Remarks. (SRS_REQ_504_1)

2.  TFMS displays a remarks entry form. (SRS_REQ_504_2)

3.  The Lecturer enters remarks and submits. (SRS_REQ_504_3)

4.  TFMS stores the remarks for HOD review and displays "Your remarks have been submitted for consideration." **\[R4\]** (SRS_REQ_504_4)

5.  This alternative flow ends.

**\[A4: View Historical Taskforce and Workload Records\]** (SRS_REQ_505)

1.  The Lecturer selects View Historical Records. (SRS_REQ_505_1)

2.  TFMS prompts the Lecturer to select a past academic session/semester. (SRS_REQ_505_2)

3.  The Lecturer selects a session/semester and confirms. (SRS_REQ_505_3)

4.  TFMS displays the Lecturer's taskforce assignments and workload summary for the selected past session/semester. (SRS_REQ_505_4)

5.  This alternative flow ends.

**\[A5: Download / Print Workload Summary\]** (SRS_REQ_506)

1.  The Lecturer selects Download / Print Workload Summary. (SRS_REQ_506_1)

2.  TFMS generates a workload summary suitable for appraisal purposes. (SRS_REQ_506_2)

3.  TFMS provides the summary in downloadable or printable format (e.g., PDF). (SRS_REQ_506_3)

4.  This alternative flow ends.

**3.2.5.4.3 Exception Flow**

None.

**3.2.5.4.4 Post-Condition(s)**

-   Lecturer has viewed current and/or historical taskforce/workload information.

-   Remarks (if submitted) are stored and available for HOD review.

**3.2.5.4.5 Rule(s) and Constraint(s)**

-   **\[R4: Audit Logging Rule\]** TFMS records an audit log entry.

-   **\[R7: Loaded Rule\]** Under-loaded \<= 5, Balanced \< 10, Over-loaded \> 10.

**3.2.5.4.6 GUI**

N/A

## 3.2.6 UC-600 -- View Management Dashboard

### 3.2.6.1 Brief Description

This use case allows Faculty Management to view executive dashboards summarising faculty taskforce distribution and workload status, drill down to department/lecturer information, and export summary reports.

### 3.2.6.2 Characteristic of Activation

Activated when Faculty Management selects View Management Dashboard from the Management Dashboard.

### 3.2.6.3 Pre-Condition

Faculty Management is authenticated.

### 3.2.6.4 Description

#### 3.2.6.4.1 Basic Flow

1.  This use case begins when Faculty Management selects one of the following options: (SRS_REQ_601)

<!-- -->

a.  **\[A1: View Taskforce Distribution Overview\]** (SRS_REQ_602)

b.  **\[A2: View Workload Overview\]** (SRS_REQ_603)

c.  **\[A3: View Department Comparison\]** (SRS_REQ_604)

d.  **\[A4: Export Summary Reports\]** (SRS_REQ_605)

<!-- -->

2.  This use case ends.

#### 3.2.6.4.2 Alternative Flows

**\[A1: View Taskforce Distribution Overview\]** (SRS_REQ_602)

1.  Faculty Management selects Taskforce Distribution Overview. (SRS_REQ_602_1)

2.  TFMS displays aggregated taskforce distribution across departments. (SRS_REQ_602_2)

3.  This alternative flow ends.

**\[A2: View Workload Overview\]** (SRS_REQ_603)

1.  Faculty Management selects Workload Overview. (SRS_REQ_603_1)

2.  TFMS displays aggregated workload status distribution. (SRS_REQ_603_2)

3.  This alternative flow ends.

**\[A3: View Department Comparison\]** (SRS_REQ_604)

1.  Faculty Management selects Department Comparison. (SRS_REQ_604_1)

2.  TFMS displays department-level comparison summaries. (SRS_REQ_604_2)

3.  This alternative flow ends.

**\[A4: Export Summary Reports\]** (SRS_REQ_605)

1.  Faculty Management selects Export Summary Reports. (SRS_REQ_605_1)

2.  Faculty Management selects report type and format (PDF/Excel). (SRS_REQ_605_2)

3.  TFMS generates and provides the exported summary. (SRS_REQ_605_3)

4.  This alternative flow ends.

### 3.2.6.4.3 Exception Flow

None.

### 3.2.6.4.4 Post-Condition(s)

Management information may be viewed; summaries may be exported.

### 3.2.6.4.5 Rule(s) and Constraint(s)

### 3.2.6.4.6 GUI

N/A