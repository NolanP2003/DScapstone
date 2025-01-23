create database mercurycorpsample;
use mercurycorpsample;

CREATE TABLE IF NOT EXISTS care(
care_id INT NOT NULL auto_increment,
notes longtext,
PRIMARY KEY (care_id)
);
CREATE TABLE IF NOT EXISTS departments(
dept_id INT NOT NULL auto_increment,
dept_name varchar(45),
PRIMARY KEY (dept_id)
);
CREATE TABLE IF NOT EXISTS medical_records(
rec_id INT NOT NULL auto_increment,
res_id CHAR(35),
vaccinations varchar(100),
allergies varchar(100),
admission_date DATE,
discharge_date DATE,
PRIMARY KEY (rec_id)
);
CREATE TABLE IF NOT EXISTS employees(
emp_id INT(4) ZEROFILL auto_increment NOT NULL,
first_name varchar(100) NOT NULL,
last_name varchar(100) NOT NULL,
mobile_no varchar(15), 
dob date,
job_title varchar(100),
salary decimal(10,2),
email  varchar(100) UNIQUE NOT NULL,
department_id INT,
address varchar(200),
PRIMARY KEY (emp_id),
FOREIGN KEY (department_id) REFERENCES departments(dept_id)
) auto_increment = 0001;
CREATE TABLE IF NOT EXISTS physician(
physician_id INT(2) ZEROFILL auto_increment NOT NULL,
first_name varchar(100) NOT NULL,
last_name varchar(100) NOT NULL,
specialty varchar(45),
email varchar(255) UNIQUE NOT NULL,
PRIMARY KEY (physician_id)
) auto_increment = 01;

CREATE TABLE IF NOT EXISTS clinical_notes(
note_id INT NOT NULL AUTO_INCREMENT,
current_illness varchar(100),
medication_refusal BOOLEAN,
diet_plan varchar(100),
emergency_calls varchar(100),
ishospice BOOLEAN,
physician_id INT(2) ZEROFILL NOT NULL,
note longtext,
rec_id INT,
PRIMARY KEY (note_id),
FOREIGN KEY (physician_id) REFERENCES physician (physician_id),
FOREIGN KEY (rec_id) REFERENCES medical_records (rec_id)
);
CREATE TABLE IF NOT EXISTS physician_orders(
order_id INT AUTO_INCREMENT,
rec_id INT,
order_date DATE,
order_text longtext,
physician_id INT(2) ZEROFILL NOT NULL,
PRIMARY KEY (order_id),
FOREIGN KEY (rec_id) REFERENCES medical_records(rec_id),
FOREIGN KEY (physician_id) REFERENCES physician (physician_id)
);
CREATE TABLE IF NOT EXISTS meds_treats(
type_id INT AUTO_INCREMENT,
type_name varchar(50),
emp_id INT(4) ZEROFILL NOT NULL,
datetime_given DATETIME,
notes varchar(100),
medication_refused BOOLEAN,
order_id INT,
PRIMARY KEY (type_id),
FOREIGN KEY (order_id) REFERENCES physician_orders(order_id),
FOREIGN KEY (emp_id) REFERENCES employees (emp_id)
);

CREATE TABLE IF NOT EXISTS accounting_record(
account_id INT NOT NULL AUTO_INCREMENT,
insurance varchar(45) NOT NULL,
monthly_rent float,
PRIMARY KEY (account_id)
);
CREATE TABLE IF NOT EXISTS visits (
visit_id INT NOT NULL AUTO_INCREMENT,
date date,
visit_type varchar(45),
physician_id INT(2) ZEROFILL NOT NULL,
note_id INT,
primary key (visit_id),
foreign key (physician_id) references physician (physician_id),
foreign key (note_id) references clinical_notes(note_id)
);

CREATE TABLE IF NOT EXISTS residential_sector(
unit_id INT NOT NULL AUTO_INCREMENT,
unit varchar(100),
phone varchar(20),
location varchar(100),
PRIMARY KEY (unit_id)
);
CREATE TABLE IF NOT EXISTS residents (
    res_id CHAR(35) NOT NULL ,
    first_name varchar(100) NOT NULL,
    last_name varchar(100) NOT NULL,
    dob date,
    mailing_address varchar(100),
    physician_id INT(2) ZEROFILL NOT NULL,
    account_id INT,
    ssn char(11) NOT NULL,
    emergency_contact INT NOT NULL,
    care_id INT,
    rec_id INT,
    unit_id INT,
    address varchar (100),
    PRIMARY KEY (res_id),
    FOREIGN KEY (physician_id) REFERENCES physician (physician_id),
    FOREIGN KEY (account_id) REFERENCES accounting_record (account_id),
    FOREIGN KEY (care_id) REFERENCES care (care_id),
    FOREIGN KEY (rec_id) REFERENCES medical_records(rec_id),
    FOREIGN KEY (unit_id) REFERENCES residential_sector(unit_id) 
);
CREATE TABLE users (
    user_id int auto_increment,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role varchar(25) NOT NULL,  
    id CHAR(36) NOT NULL ,
    PRIMARY KEY (user_id)
);

INSERT INTO departments (dept_name) VALUES ("Nursing");
INSERT INTO departments (dept_name) VALUES ("Accounting");
INSERT INTO departments (dept_name) VALUES ("HR");
INSERT INTO departments (dept_name) VALUES ("Records");

INSERT INTO employees
(first_name, last_name, mobile_no, dob, job_title, salary, email, department_id, address)
VALUES
('Martin', 'Donna', 2122222222, '1990-03-05', 'HR Supervisor', 50000, 'donnam', 3, '12 Willow Road');

INSERT INTO employees
(first_name, last_name, mobile_no, dob, job_title, salary, email, department_id, address)
VALUES
('Beverly', 'Banks', 2124444444, '1995-09-07', 'Accountant', 40000, 'banksb', 2, '911 Market Street');

INSERT INTO employees
(first_name, last_name, mobile_no, dob, job_title, salary, email, department_id, address)
VALUES
('Daniel', 'Doe', 2178890000, '1986-03-05', 'CNA', 58000, 'doed', 1, '212 Willow Road');

INSERT INTO residential_sector
(unit, phone, location)
VALUES
('Personal Care', 3133330000, 'East Wing');

INSERT INTO residential_sector
(unit, phone, location)
VALUES
('Independent Living', 3133339999, 'West Wing');

INSERT INTO residential_sector
(unit, phone, location)
VALUES
('Skilled Care', 3133330870, 'South Wing');

INSERT INTO physician (first_name, last_name, specialty, email)
VALUES ('Mary', 'Fisher', 'Endocrinology', 'fisherm');

INSERT INTO physician (first_name, last_name, specialty, email)
VALUES ('Sophie', 'Collins', 'Geriatrics', 'collinss');

INSERT INTO accounting_record (insurance, monthly_rent) VALUES ('United Care', 800);
INSERT INTO accounting_record (insurance, monthly_rent) VALUES ('United Care', 900);

INSERT INTO care (notes) VALUES ('Likes to eat lunch at 12:00 noon.');
INSERT INTO care (notes) VALUES ('Goes to bed at 8:00 pm with.');
INSERT INTO care (notes) VALUES ('Eats breakfast at 8:00 am.');

INSERT INTO medical_records
(res_id,vaccinations, allergies)
VALUES
('R001','Flu', 'Nut');

INSERT INTO medical_records
(res_id,vaccinations, allergies)
VALUES
('R001','HPV', 'Milk');

INSERT INTO clinical_notes
(current_illness, medication_refusal, diet_plan, emergency_calls, physician_id, note, rec_id)
VALUES
('Peripheral Artery Disease', 0,'mediterranean diet', 'none',01, 'needs checked on regulary', 1);

INSERT INTO clinical_notes
(current_illness, medication_refusal, diet_plan, emergency_calls, physician_id, note, rec_id)
VALUES
('High Blood Pressure', 0,'grains', 'none',02, 'needs checked on regulary', 2);

INSERT INTO clinical_notes 
(current_illness, medication_refusal, diet_plan, emergency_calls, physician_id, note, rec_id)
VALUES 
('Peripheral Artery Disease', 0, 'mediterranean diet', 'none', 01, 'needs visual checks', 1);

INSERT INTO clinical_notes 
(current_illness, medication_refusal, diet_plan, emergency_calls, physician_id, note, rec_id)
VALUES 
('High Blood Pressure', 0, 'grains', 'none', 02, 'needs visual checks', 2);

INSERT INTO residents 
(res_id,first_name, last_name, dob, mailing_address, physician_id,account_id, ssn, emergency_contact, care_id, rec_id, unit_id, address)
VALUES
('R001','John', 'Doe', '1995-09-09', '13 Malo Street', 01, 1, 238179900, 2126579999,1,1,1,'E');

INSERT INTO residents 
(res_id,first_name, last_name, dob, mailing_address, physician_id,account_id, ssn, emergency_contact, care_id, rec_id, unit_id, address)
VALUES
('R002','Joys', 'Munchkin', '1960-12-09', '13 Alpha Street', 02, 2, 347878801, 2126579999,2,2,1,'E');

ALTER TABLE employees
ADD hire_date DATE;

UPDATE employees
SET hire_date = '2021-11-01'
WHERE emp_id = 0001; 

UPDATE employees
SET hire_date = '2021-06-15'
WHERE emp_id = 0002;

UPDATE employees
SET hire_date = '2019-10-15'
WHERE emp_id = 0003;