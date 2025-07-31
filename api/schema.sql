CREATE TABLE IF NOT EXISTS ConsoleDetails (
    ID INT PRIMARY KEY,
    ConsoleName VARCHAR(50) NOT NULL UNIQUE,
    Publisher VARCHAR(20) NOT NULL,
    Released YEAR NOT NULL,
    Discontinued YEAR NULL,
    OriginalPrice DECIMAL(5,2) NOT NULL,
    CurrentPrice DECIMAL(5,2) NOT NULL,
    Generation INT NOT NULL,
    ImageURL TEXT
);

CREATE TABLE IF NOT EXISTS ConsolePrice (
    ID INT PRIMARY KEY,
    Console VARCHAR(50) NOT NULL,
    Date DATE NOT NULL,
    Price DECIMAL(5,2) NOT NULL
);

CREATE TABLE IF NOT EXISTS ConsoleGen (
    ID INT PRIMARY KEY,
    Generation INT NOT NULL,
    Console VARCHAR(50) NOT NULL,
    Released YEAR NOT NULL,
    ImageURL TEXT
);

CREATE TABLE IF NOT EXISTS ConsoleSales (
    ID INT PRIMARY KEY,
    Platform VARCHAR(20) NOT NULL,
    Console VARCHAR(50) NOT NULL,
    Region VARCHAR(20) NOT NULL,
    Sales DECIMAL(5,2),
    ImageURL TEXT
);

CREATE TABLE IF NOT EXISTS Games (
    ID INT PRIMARY KEY,
    Name VARCHAR(255),
    Released DATE,
    Rating DECIMAL(5,2),
    Genre TEXT,
    Platform TEXT,
    Developer TEXT,
    Publisher TEXT,
    ESRB VARCHAR(20),
    Metacritic INT,
    ImageURL TEXT,  
    Description TEXT
);

CREATE TABLE IF NOT EXISTS Subscriptions (
    ID INT PRIMARY KEY,
    Platform VARCHAR(20) NOT NULL,
    SubscriptionName VARCHAR(100) NOT NULL,
    Tier VARCHAR(10) NOT NULL,
    Console TEXT NOT NULL,
    Duration INT NOT NULL,
    Price DECIMAL(5,2) NOT NULL,
    Benefits TEXT NOT NULL,
    OfficialURL TEXT NOT NULL
);