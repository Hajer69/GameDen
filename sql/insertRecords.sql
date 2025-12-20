-- ============================================
-- INSERT RECORDS
-- ============================================

USE gamedendb;

-- Insert sample games
INSERT INTO games (game_name, category, difficulty, players, rating) VALUES
('Rock Paper Scissors', 'Strategy', 'Easy', '1-2 Players',4.5 ),
('Sudoku Challenge', 'Puzzle', 'Medium', '1 Player', 4.7),
('Tic Tac Toe ', 'Strategy', 'Easy', '2 Players', 4.3),
('Brain Quiz', 'Knowledge', 'Medium', '1 Player', 4.8), 
('Connect Four', 'Strategy', 'Easy', '2 Players', 4.6);

-- Insert sample categories
INSERT INTO game_categories (category_name, game_count, description, skills_developed) VALUES
('Strategy', 2, 'Games that require planning', 'Decision Making'),
('Puzzle', 1, 'Games that challenge thinking', 'Problem Solving'),
('Knowledge', 1, 'Games that test Knowledge','Learning games');

-- Insert sample user info
INSERT INTO users (username, password, email, fullName, favoriteGame) VALUES
('hajer_s', 'hajer22', 'hajer@email.com', 'Hajer Said', 'Rock Paper Scissors'),
('fatima_k', 'fatima32', 'fatima@email.com', 'Fatima Karaa', 'Sudoku'),
('muna_s', 'muna11', 'muna@email.com', 'Muna Salim', 'Tic Tac Toe'),
('reem_m', 'reem55', 'Reem@email.com', 'Reem Mohhamed', 'Brain Quiz'),
('sara_a', 'sara40', 'Sara@email.com', 'Sara Ahmed', 'Sudoku');