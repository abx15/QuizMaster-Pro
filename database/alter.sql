-- Add category and explanation columns to questions table
ALTER TABLE questions 
ADD COLUMN category VARCHAR(50) DEFAULT 'general',
ADD COLUMN explanation TEXT NULL;

-- Update sample questions with categories and explanations
UPDATE questions SET 
category = 'cs',
explanation = 'PHP originally stood for Personal Home Page, but now stands for PHP: Hypertext Preprocessor (a recursive acronym).'
WHERE question LIKE '%PHP stand for%';

UPDATE questions SET 
category = 'cs',
explanation = 'The semicolon (;) is used to terminate statements in PHP, similar to many other programming languages like C, Java, and JavaScript.'
WHERE question LIKE '%end of a PHP statement%';

UPDATE questions SET 
category = 'math',
explanation = 'The modulus operator (%) returns the remainder of a division operation. 8 divided by 3 is 2 with remainder 2.'
WHERE question LIKE '%result of 8 % 3%';

UPDATE questions SET 
category = 'cs',
explanation = 'The header() function is used to send raw HTTP headers, including redirects using the Location header.'
WHERE question LIKE '%redirect in PHP%';

UPDATE questions SET 
category = 'cs',
explanation = 'SQL stands for Structured Query Language, which is the standard language for relational database management systems.'
WHERE question LIKE '%SQL stand for%';



-- +++++++++ -----


-- Add category and explanation columns to questions table if they don't exist
ALTER TABLE questions 
ADD COLUMN IF NOT EXISTS category VARCHAR(50) DEFAULT 'general',
ADD COLUMN IF NOT EXISTS explanation TEXT NULL;

-- Update existing questions with categories (optional)
UPDATE questions SET category = 'cs' WHERE question LIKE '%PHP%' OR question LIKE '%SQL%';
UPDATE questions SET category = 'math' WHERE question LIKE '%8 % 3%';
UPDATE questions SET category = 'gk' WHERE category = 'general' OR category IS NULL;

-- Add some sample explanations
UPDATE questions SET explanation = 'PHP originally stood for Personal Home Page, but now stands for PHP: Hypertext Preprocessor (a recursive acronym).' WHERE question LIKE '%PHP stand for%';
UPDATE questions SET explanation = 'The semicolon (;) is used to terminate statements in PHP, similar to many other programming languages like C, Java, and JavaScript.' WHERE question LIKE '%end of a PHP statement%';
UPDATE questions SET explanation = 'The modulus operator (%) returns the remainder of a division operation. 8 divided by 3 is 2 with remainder 2.' WHERE question LIKE '%result of 8 % 3%';