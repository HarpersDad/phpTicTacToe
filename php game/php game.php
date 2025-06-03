<?php
session_start();

function init() {
    $_SESSION['board'] = array_fill(0, 9, '');
    $_SESSION['turn'] = 'X';
    $_SESSION['winner'] = '';
    $_SESSION['winningSquares'] = [];
}

function checkWin($b) {
    $winningPatterns = [
        [0,1,2],[3,4,5],[6,7,8],  // rows
        [0,3,6],[1,4,7],[2,5,8],  // cols
        [0,4,8],[2,4,6]           // diagonals
    ];
    foreach ($winningPatterns as $pattern) {
        if ($b[$pattern[0]] && $b[$pattern[0]] === $b[$pattern[1]] && $b[$pattern[1]] === $b[$pattern[2]]) {
            return [$b[$pattern[0]], $pattern];
        }
    }
    return in_array('', $b) ? ['', []] : ['Draw', []];
}

if (!isset($_SESSION['board'])) init();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['reset'])) {
        init();
    } elseif (isset($_POST['move']) && ctype_digit($_POST['move'])) {
        $i = (int)$_POST['move'];
        if ($i >= 0 && $i < 9 && $_SESSION['board'][$i] === '' && $_SESSION['winner'] === '') {
            $_SESSION['board'][$i] = $_SESSION['turn'];
            list($winner, $winningSquares) = checkWin($_SESSION['board']);
            $_SESSION['winner'] = $winner;
            $_SESSION['winningSquares'] = $winningSquares;
            if ($_SESSION['winner'] === '') {
                $_SESSION['turn'] = $_SESSION['turn'] === 'X' ? 'O' : 'X';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tic-Tac-Toe</title>
    <style>
        .board-form {
            display: grid;
            grid-template-columns: repeat(3, 80px);
            gap: 0; /* remove gap so borders line up */
            margin: 2em auto;
            justify-content: center;
            width: max-content;
            border: 2px solid #333;
        }
        button {
            padding: 0;
            border: 1px solid #333;
            cursor: pointer;
            background: none;
            outline: none;
        }
        button:disabled {
            cursor: default;
            background-color: #f9f9f9;
        }
        img {
            display: block;
            width: 80px;
            height: 80px;
        }
        body {
            text-align: center;
            font-family: sans-serif;
        }
    </style>
</head>
<body>
    <h2>Tic-Tac-Toe</h2>
    <p>
        <?= $_SESSION['winner']
            ? ($_SESSION['winner'] === 'Draw' ? 'Draw' : $_SESSION['winner'] . ' wins!')
            : 'Turn: ' . $_SESSION['turn'] ?>
    </p>

    <form method="post" class="board-form">
        <?php foreach ($_SESSION['board'] as $i => $v): ?>
            <?php
                $img = 'images/ttt_b.png';
                if ($v === 'X') $img = 'images/ttt_x.png';
                elseif ($v === 'O') $img = 'images/ttt_o.png';

                if ($_SESSION['winner'] !== '' && $_SESSION['winner'] !== 'Draw' && in_array($i, $_SESSION['winningSquares'])) {
                    $img = 'images/ttt_win_' . strtolower($_SESSION['winner']) . '.png';
                }
            ?>
            <button type="submit" name="move" value="<?= $i ?>" <?= $v || $_SESSION['winner'] ? 'disabled' : '' ?>>
                <img src="<?= $img ?>" alt="<?= $v ?: 'Blank' ?>">
            </button>
        <?php endforeach; ?>
    </form>

    <form method="post">
        <button type="submit" name="reset">Reset</button>
    </form>
</body>
</html>
