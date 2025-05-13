-- Add Genres
INSERT INTO Genre (Name) VALUES
    ('Fiction'),
    ('Sci-Fi'),
    ('Fantasy'),
    ('Non-Fiction'),
    ('Mystery'),
    ('Biography'),
    ('Romance'),
    ('Historical');

-- Add Authors
INSERT INTO Author (Name) VALUES
    ('J.K. Rowling'),
    ('Isaac Asimov'),
    ('Toni Morrison'),
    ('Yuval Noah Harari'),
    ('Agatha Christie'),
    ('Michelle Obama'),
    ('Jane Austen'),
    ('Erik Larson'),
    ('Neil Gaiman'),
    ('Ursula K. Le Guin'),
    ('Haruki Murakami'),
    ('Chimamanda Ngozi Adichie'),
    ('Stephen King'),
    ('Malala Yousafzai'),
    ('Colson Whitehead'),
    ('Sally Rooney'),
    ('Andy Weir'),
    ('Zadie Smith'),
    ('Kazuo Ishiguro'),
    ('Ann Patchett');

-- Add Books
INSERT INTO Book (Title, ISBN, PublicationYear) VALUES
    ('Harry Potter and the Sorcerer\'s Stone', '9780590353427', 1997),
    ('Foundation', '9780553293357', 1951),
    ('Beloved', '9781400033416', 1987),
    ('Sapiens: A Brief History of Humankind', '9780062316097', 2014),
    ('Murder on the Orient Express', '9780062693662', 1934),
    ('Becoming', '9781524763138', 2018),
    ('Pride and Prejudice', '9780141439518', 1813),
    ('The Devil in the White City', '9780375725609', 2003),
    ('American Gods', '9780062572233', 2001),
    ('A Wizard of Earthsea', '9780547773742', 1968),
    ('Norwegian Wood', '9780375704024', 1987),
    ('Half of a Yellow Sun', '9781400095209', 2006),
    ('The Shining', '9780307743657', 1977),
    ('I Am Malala', '9780316322409', 2013),
    ('The Underground Railroad', '9780385542364', 2016),
    ('Normal People', '9781984822185', 2018),
    ('Project Hail Mary', '9780593135204', 2021),
    ('White Teeth', '9780375703867', 2000),
    ('Never Let Me Go', '9781400078776', 2005),
    ('Commonwealth', '9780062491831', 2016);

-- Link Books to Authors (WrittenBy)
INSERT INTO WrittenBy (Book_ID, Author_ID) VALUES
    (1, 1),  -- Harry Potter / J.K. Rowling
    (2, 2),  -- Foundation / Isaac Asimov
    (3, 3),  -- Beloved / Toni Morrison
    (4, 4),  -- Sapiens / Yuval Noah Harari
    (5, 5),  -- Murder / Agatha Christie
    (6, 6),  -- Becoming / Michelle Obama
    (7, 7),  -- Pride / Jane Austen
    (8, 8),  -- Devil / Erik Larson
    (9, 9),  -- American Gods / Neil Gaiman
    (10, 10), -- Earthsea / Ursula K. Le Guin
    (11, 11), -- Norwegian Wood / Haruki Murakami
    (12, 12), -- Half of a Yellow Sun / Chimamanda Ngozi Adichie
    (13, 13), -- The Shining / Stephen King
    (14, 14), -- I Am Malala / Malala Yousafzai
    (15, 15), -- Underground Railroad / Colson Whitehead
    (16, 16), -- Normal People / Sally Rooney
    (17, 17), -- Project Hail Mary / Andy Weir
    (18, 18), -- White Teeth / Zadie Smith
    (19, 19), -- Never Let Me Go / Kazuo Ishiguro
    (20, 20); -- Commonwealth / Ann Patchett

-- Link Books to Genres (BookGenre)
INSERT INTO BookGenre (Book_ID, Genre_ID) VALUES
    (1, 3),  -- Harry Potter / Fantasy
    (2, 2),  -- Foundation / Sci-Fi
    (3, 1),  -- Beloved / Fiction
    (4, 4),  -- Sapiens / Non-Fiction
    (5, 5),  -- Murder / Mystery
    (6, 6),  -- Becoming / Biography
    (7, 7),  -- Pride / Romance
    (8, 8),  -- Devil / Historical
    (9, 3),  -- American Gods / Fantasy
    (10, 3), -- Earthsea / Fantasy
    (11, 1), -- Norwegian Wood / Fiction
    (12, 8), -- Half of a Yellow Sun / Historical
    (13, 5), -- The Shining / Mystery
    (14, 6), -- I Am Malala / Biography
    (15, 8), -- Underground Railroad / Historical
    (16, 7), -- Normal People / Romance
    (17, 2), -- Project Hail Mary / Sci-Fi
    (18, 1), -- White Teeth / Fiction
    (19, 1), -- Never Let Me Go / Fiction
    (20, 1); -- Commonwealth / Fiction

-- Add Members
INSERT INTO Member (Name, Email, Password, Role) VALUES
    ('Emma Davis', 'emma.davis@example.com', 'password1', 'member'),
    ('Liam Wilson', 'liam.wilson@example.com', 'password2', 'member'),
    ('Olivia Brown', 'olivia.brown@example.com', 'password3', 'member'),
    ('Noah Taylor', 'noah.taylor@example.com', 'password4', 'member'),
    ('Ava Martinez', 'ava.martinez@example.com', 'password5', 'member'),
    ('Sophia Anderson', 'sophia.anderson@example.com', 'password6', 'member'),
    ('James Thomas', 'james.thomas@example.com', 'password7', 'member'),
    ('Isabella Lee', 'isabella.lee@example.com', 'password8', 'member'),
    ('Lucas Garcia', 'lucas.garcia@example.com', 'password9', 'member'),
    ('Mia Rodriguez', 'mia.rodriguez@example.com', 'password10', 'member');

-- Add Librarians
INSERT INTO Member (Name, Email, Password, Role) VALUES
    ('Charlotte White', 'charlotte.white@example.com', 'libpass1', 'librarian'),
    ('Henry Clark', 'henry.clark@example.com', 'libpass2', 'librarian'),
    ('Amelia Lewis', 'amelia.lewis@example.com', 'libpass3', 'librarian'),
    ('Elijah Walker', 'elijah.walker@example.com', 'libpass4', 'librarian'),
    ('Harper Hall', 'harper.hall@example.com', 'libpass5', 'librarian');