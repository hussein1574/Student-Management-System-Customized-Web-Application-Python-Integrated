# Student Management System - Customized and Python-Integrated

This project is a highly adaptable and scalable student management system with a modular architecture. It includes a set of APIs for the student React app and an admin panel designed specifically for professors, academic advisors, and administrators. Noteworthy customizations and Python integration have been implemented to enhance the system's functionality.

## Admin Panel Customizations

- **Role-Based Access Control**: Users are granted access to specific pages based on their assigned role.
- **Custom Dashboard**: A personalized dashboard provides statistics on failed students, registered students, and course information. Custom buttons are integrated to facilitate opening/closing student registrations and clearing registrations.
- **Students Table Customization**: A custom button appears upon completion of course registration, redirecting to a page displaying the student's registered, finished, and available courses. Admins and academic advisors can then admit or modify courses. Additionally, a custom button allows for the export of a student's transcript as a PDF.
- **CSV File Upload**: A dedicated page enables admins to upload CSV files containing regulation courses for specific departments.
- **Exam Timetable Generation**: A custom page allows users to specify a starting date and generate the exam timetable. Python integration is employed, with the admin panel creating CSV files, running a Python script, and populating the database with the generated timetable.
- **Student Results Management**: Two custom pages handle student results. One is designed for professors, enabling them to enter student names, IDs, and grades for the courses they teach. The other page, for admins, features a table displaying failure rates, success rates, and student counts per grade. Admins can drop results for specific subjects, add grades for all students, and admit the results.

## Technologies Used

- Python integration
- React
- API development
- Role-based access control
- CSV file processing
- Database management

This project showcases the implementation of various customizations and Python integration within a student management system. It highlights your ability to create tailored solutions and integrate diverse technologies to optimize academic administration.

For detailed documentation and code, please refer to the project repository.
