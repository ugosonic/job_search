README

Project Title: Job Seeker Portal 


 DESCRIPTION: 
Below is a features in Job seeker website: 
1. User registration and authentication: Allow users  to create an accounts either as  AllUsers or an Employer.

2. Resume/CV submission and generation of CV to pdf: Allow job searchers to manage their resumes or CVs for potential companies to view and also allows job searchers to generate their CV in pdf file on the portal. 

3. CV search: This will give employers access to search CV based on different criteria like Job or Sector Preference, Minimum Education Level, Minimum Number of GCSE Passes, Specific Educational Qualification, Specific Professional Qualification, Specific Skill, Experience. 


TECHNOLOGIES  USED:

Frontend: HTML, CSS, JavaScript
Backend: PHP, MySQL
Frameworks/Libraries: Bootstrap, jQuery
Additional Tools: Font Awesome (for icons), TCPDF (for PDF generation).


Setup Instructions:

1. Download XAMPP: Visit [https://www.apachefriends.org/download.html], download XAMPP, and then follow the installation steps. XAMPP allows you to run Apache and MySQL on your PC as a server.

2. Start Apache and MySQL: Launch XAMPP and start both Apache and MySQL services.

3. Copy Job Search Folder: From the downloaded files, copy the "Job Search" folder. Navigate to your local computer's 'C' drive, then open the XAMPP folder, followed by the 'htdocs' folder. Paste the "Job Search" folder into the 'htdocs' directory.

4. **Access phpMyAdmin**: Launch your web browser and enter 'localhost/phpmyadmin' into the address box.

5. Create Login Database: - Create a new database called 'phplogin'. 
   - Go to the folder in the downloaded unzipped file and copy the file 'phplogin.sql'. 
   - Navigate to the folder in the downloaded zip file, and copy the file named `phplogin.sql`. 
   - In phpMyAdmin, select the `phplogin` database, click on the "Import" tab, and upload the `phplogin.sql` file.

6. **Create CV Submission Database**: 
   - Create another new database named `cv_submit`. 
   - Similarly, navigate to the folder in the downloaded zip file, and copy the file named `cv_submit.sql`. 
   - In phpMyAdmin, select the `cv_submit` database, click on the "Import" tab, and upload the `cv_submit.sql` file.

7. **Access Job Search Website**: 
   - Finally, in your web browser, type `http://localhost/Job Search` in the address bar to access the Job Search website.









5. Copy the file named php



Credits
This project was developed by Kingsley Ugonna Aguagwa. 

Contributions, bug reports, and feedback are welcome.


