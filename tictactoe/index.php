<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tic Tac Toe</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .game-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 2.5rem;
        }

        .game-info {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 1.2rem;
        }

        .current-player {
            font-weight: bold;
            color: #e74c3c;
        }

        .board {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin: 20px auto;
            max-width: 300px;
        }

        .cell {
            aspect-ratio: 1;
            background: #ecf0f1;
            border: none;
            border-radius: 8px;
            font-size: 2.5rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cell:hover {
            background: #d5dbdb;
            transform: scale(1.05);
        }

        .cell.x {
            color: #e74c3c;
        }

        .cell.o {
            color: #3498db;
        }

        .cell.winner {
            background: #2ecc71;
            color: white;
        }

        .controls {
            margin-top: 20px;
        }

        .btn {
            padding: 12px 30px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
            margin: 0 5px;
        }

        .btn:hover {
            background: #2980b9;
        }

        .btn-reset {
            background: #e74c3c;
        }

        .btn-reset:hover {
            background: #c0392b;
        }

        .message {
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
        }

        .message.win {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.draw {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .score-board {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }

        .score-item {
            text-align: center;
        }

        .score-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2c3e50;
        }

        @media (max-width: 480px) {
            .game-container {
                padding: 20px;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .cell {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="game-container">
        <h1>Tic Tac Toe</h1>
        
        <?php
        session_start();

        // Initialize game state if not set
        if (!isset($_SESSION['board'])) {
            initializeGame();
        }

        // Handle reset request
        if (isset($_POST['reset'])) {
            initializeGame();
        }

        // Handle cell click
        if (isset($_POST['cell'])) {
            $cellIndex = $_POST['cell'];
            makeMove($cellIndex);
        }

        // Initialize game function
        function initializeGame() {
            $_SESSION['board'] = array_fill(0, 9, '');
            $_SESSION['currentPlayer'] = 'X';
            $_SESSION['gameOver'] = false;
            $_SESSION['winner'] = null;
            $_SESSION['winningLine'] = [];
            if (!isset($_SESSION['scores'])) {
                $_SESSION['scores'] = ['X' => 0, 'O' => 0, 'draws' => 0];
            }
        }

        // Make a move function
        function makeMove($cellIndex) {
            if (!$_SESSION['gameOver'] && $_SESSION['board'][$cellIndex] === '') {
                $_SESSION['board'][$cellIndex] = $_SESSION['currentPlayer'];
                
                if (checkWinner($_SESSION['currentPlayer'])) {
                    $_SESSION['gameOver'] = true;
                    $_SESSION['winner'] = $_SESSION['currentPlayer'];
                    $_SESSION['scores'][$_SESSION['currentPlayer']]++;
                } elseif (checkDraw()) {
                    $_SESSION['gameOver'] = true;
                    $_SESSION['scores']['draws']++;
                } else {
                    // Switch player
                    $_SESSION['currentPlayer'] = ($_SESSION['currentPlayer'] === 'X') ? 'O' : 'X';
                }
            }
        }

        // Check for winner function
        function checkWinner($player) {
            $board = $_SESSION['board'];
            $winningLines = [
                [0, 1, 2], [3, 4, 5], [6, 7, 8], // Rows
                [0, 3, 6], [1, 4, 7], [2, 5, 8], // Columns
                [0, 4, 8], [2, 4, 6]             // Diagonals
            ];

            foreach ($winningLines as $line) {
                if ($board[$line[0]] === $player && 
                    $board[$line[1]] === $player && 
                    $board[$line[2]] === $player) {
                    $_SESSION['winningLine'] = $line;
                    return true;
                }
            }
            return false;
        }

        // Check for draw function
        function checkDraw() {
            return !in_array('', $_SESSION['board']) && !$_SESSION['winner'];
        }

        // Display score board
        echo '<div class="score-board">';
        echo '<div class="score-item">';
        echo '<div>Player X</div>';
        echo '<div class="score-value">' . $_SESSION['scores']['X'] . '</div>';
        echo '</div>';
        echo '<div class="score-item">';
        echo '<div>Draws</div>';
        echo '<div class="score-value">' . $_SESSION['scores']['draws'] . '</div>';
        echo '</div>';
        echo '<div class="score-item">';
        echo '<div>Player O</div>';
        echo '<div class="score-value">' . $_SESSION['scores']['O'] . '</div>';
        echo '</div>';
        echo '</div>';

        // Display game info
        echo '<div class="game-info">';
        if ($_SESSION['gameOver']) {
            if ($_SESSION['winner']) {
                echo '<div class="message win">Player ' . $_SESSION['winner'] . ' wins!</div>';
            } else {
                echo '<div class="message draw">It\'s a draw!</div>';
            }
        } else {
            echo 'Current player: <span class="current-player">' . $_SESSION['currentPlayer'] . '</span>';
        }
        echo '</div>';
        ?>

        <form method="POST" class="board">
            <?php
            for ($i = 0; $i < 9; $i++) {
                $cellValue = $_SESSION['board'][$i];
                $isWinningCell = in_array($i, $_SESSION['winningLine']);
                $cellClass = $cellValue ? strtolower($cellValue) : '';
                if ($isWinningCell) {
                    $cellClass .= ' winner';
                }
                
                echo '<button type="submit" name="cell" value="' . $i . '" 
                      class="cell ' . $cellClass . '" ' . 
                      ($_SESSION['gameOver'] || $cellValue ? 'disabled' : '') . '>';
                echo $cellValue;
                echo '</button>';
            }
            ?>
        </form>

        <div class="controls">
            <form method="POST">
                <button type="submit" name="reset" class="btn btn-reset">New Game</button>
            </form>
        </div>

        <div style="margin-top: 20px; font-size: 0.9rem; color: #666;">
            <p><strong>How to play:</strong> Players take turns placing X and O marks in the grid. The first player to get 3 of their marks in a row (up, down, across, or diagonally) wins.</p>
        </div>
    </div>

    <script>
        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            const cells = document.querySelectorAll('.cell');
            
            cells.forEach(cell => {
                cell.addEventListener('mouseenter', function() {
                    if (!this.disabled && !this.textContent) {
                        this.textContent = '<?php echo $_SESSION["gameOver"] ? "" : $_SESSION["currentPlayer"]; ?>';
                        this.style.opacity = '0.5';
                    }
                });
                
                cell.addEventListener('mouseleave', function() {
                    if (!this.disabled && !this.classList.contains('x') && !this.classList.contains('o')) {
                        this.textContent = '';
                        this.style.opacity = '1';
                    }
                });
            });
        });
    </script>
</body>
</html>