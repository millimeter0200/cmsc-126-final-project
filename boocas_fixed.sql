-- Drop existing tables if they exist (for clean import)
DROP TABLE IF EXISTS reservation;
DROP TABLE IF EXISTS manages;
DROP TABLE IF EXISTS timeslot;
DROP TABLE IF EXISTS student;
DROP TABLE IF EXISTS admin;
DROP TABLE IF EXISTS room;

-- Create student table
CREATE TABLE student (
    studentID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    password VARCHAR(255)
);

-- Create admin table
CREATE TABLE admin (
    adminID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    password VARCHAR(255)
);

-- Create room table
CREATE TABLE room (
    roomID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    type VARCHAR(50),
    capacity INT
);

-- Create timeslot table
CREATE TABLE timeslot (
    slotID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    start_time TIME,
    end_time TIME
);

-- Create reservation table
CREATE TABLE reservation (
    reservationID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    studentID INT,
    roomID INT,
    reservation_date DATE,
    start_time TIME,
    end_time TIME,
    subjectActivity VARCHAR(255),
    purpose VARCHAR(255),
    divisionOffice VARCHAR(255),
    status VARCHAR(20),
    FOREIGN KEY (studentID) REFERENCES student(studentID) ON DELETE CASCADE,
    FOREIGN KEY (roomID) REFERENCES room(roomID) ON DELETE CASCADE
);

-- Create manages table (admin-room relationship)
CREATE TABLE manages (
    adminID INT,
    roomID INT,
    PRIMARY KEY (adminID, roomID),
    FOREIGN KEY (adminID) REFERENCES admin(adminID) ON DELETE CASCADE,
    FOREIGN KEY (roomID) REFERENCES room(roomID) ON DELETE CASCADE
);
