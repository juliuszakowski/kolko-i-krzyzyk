<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KiK</title>
    <link rel="stylesheet" href="kik.css">
</head>
<body>

<audio controls autoplay src='muzyka.mp3'></audio> <!-- muza -->

<div class="naglowek">
    <img src="naglowek.png" alt="" width="500" height="300">
</div>

<div class="lewas">
    <h2>Wybierz tryb gry</h2> <!-- wybierasz tryb -->
    <button onclick="startGame('computer')"> z komputerem </button>
    <button onclick="startGame('multiplayer')"> z innym graczem </button>
    <button onclick="startGame('network')"> sieciówka </button>

    <h2>Wybierz wojownika</h2> <!-- wybierasz X albo O -->
    <button onclick="chooseSymbol('X')">X</button>
    <button onclick="chooseSymbol('O')">O</button>

    <h2>Wyczyść planszę</h2> <!-- czyszczenie planszy -->
    <button onclick="clearBoard()">Wyczyść</button>
    <p id="instructions"> <h2>Wybierz tryb gry, wojownika i rozpocznij rozgrywkę </h2> </p>
</div>

<div class="gra">
    <table id="polagry">
        <tr>
             <td onclick="makeMove(0, 0)"></td> 
             <td onclick="makeMove(0, 1)"></td> 
             <td onclick="makeMove(0, 2)"></td>
        </tr>
        <tr>                                                     <!-- zaznaczanie pola -->
             <td onclick="makeMove(1, 0)"></td>
             <td onclick="makeMove(1, 1)"></td> 
             <td onclick="makeMove(1, 2)"></td>
        </tr>
        <tr>
             <td onclick="makeMove(2, 0)"></td>
             <td onclick="makeMove(2, 1)"></td>
             <td onclick="makeMove(2, 2)"></td>
        </tr>
    </table>
</div>

<div class="prawas">
    <h2>zegar</h2>
<p id="timer">Czas: 00:00</p>

<h2>historia wyników</h2>
<table id="scoreboard">
    <tr>
        <th>X</th>
        <th>O</th>
        <th>Remisy</th>
    </tr>
    <tr>
        <td id="xWins">0</td>
        <td id="oWins">0</td>
        <td id="draws">0</td>
    </tr>
</table>
</div>

<script>
    let currentPlayer;
    let board = ['', '', '', '', '', '', '', '', ''];
    let gameActive = false;
    let gameMode;
    let xWins = 0;
    let oWins = 0;
    let draws = 0;
    function startGame(mode) {
        console.log(`Rozpoczęto grę w trybie: ${mode}`);
        gameMode = mode;
        gameActive = true;
        currentPlayer = 'X'; // Domyślnie zaczyna X
        startTime = new Date();
        updateTimer();
        clearBoard();

        if (mode === 'computer' && currentPlayer === 'O') {
            makeComputerMove();
        }
    }

    function chooseSymbol(symbol) {
        console.log(`Wybrano symbol: ${symbol}`);
        currentPlayer = symbol;

        if (gameMode === 'computer' && currentPlayer === 'O') {
            makeComputerMove();
        }
    }

    function makeMove(row, col) {
        if (!gameActive || board[row * 3 + col] !== '') {
            return;
        }

        board[row * 3 + col] = currentPlayer;
        renderBoard();

        const winner = checkWinner();
        const draw = checkDraw();

        if (winner) {
            console.log(`Gracz ${winner} wygrywa!`);
             updateScore(winner);
            gameActive = false;
            document.getElementById('instructions').innerText = `Gracz ${winner} wygrywa! Kliknij "Wyczyść", aby zagrać ponownie.`;
        } else if (draw) {
            console.log('Remis!');
            draws++;
            updateScore();
            gameActive = false;
            document.getElementById('instructions').innerText = 'Remis! Kliknij "Wyczyść planszę", aby zagrać ponownie.';
        } else {
            currentPlayer = currentPlayer === 'X' ? 'O' : 'X';

            if (gameMode === 'computer' && currentPlayer === 'O') {
                makeComputerMove();
            }
        }
    }

    function makeComputerMove() {
        const emptyCells = board.reduce((acc, cell, index) => {
            if (cell === '') {
                acc.push(index);
            }
            return acc;
        }, []);

        if (emptyCells.length > 0) {
            const randomIndex = emptyCells[Math.floor(Math.random() * emptyCells.length)];
            const row = Math.floor(randomIndex / 3);
            const col = randomIndex % 3;

            setTimeout(() => {
                makeMove(row, col);
            }, 500); // Opóźnienie, aby zobaczyć ruch komputera
        }
    }

    function clearBoard() {
        console.log('Wyczyszczono planszę');
        board = ['', '', '', '', '', '', '', '', ''];
        renderBoard();

        if (gameMode === 'computer' && currentPlayer === 'O') {
            makeComputerMove();
        }

        document.getElementById('instructions').innerText = 'Kliknij pole, aby postawić swój symbol.';
    }

    function checkWinner() {
        const winPatterns = [
            [0, 1, 2], [3, 4, 5], [6, 7, 8], // Rows
            [0, 3, 6], [1, 4, 7], [2, 5, 8], // Columns
            [0, 4, 8], [2, 4, 6]             // Diagonals
        ];

        for (const pattern of winPatterns) {
            const [a, b, c] = pattern;
            if (board[a] && board[a] === board[b] && board[a] === board[c]) {
                return board[a];
            }
        }

        return null;
    }

    function checkDraw() {
        return !board.includes('');
    }

    function renderBoard() {
        const cells = document.querySelectorAll('#polagry td');
        cells.forEach((cell, index) => {
            const img = document.createElement('img');
            img.src = board[index] === 'X' ? 'xkik.png' : board[index] === 'O' ? 'okik.png' : '';
            img.alt = board[index];
            img.style.width = '200px';
            img.style.height = '200px';

            cell.innerHTML = '';
            cell.appendChild(img);
        });
    }
    function updateTimer() {
        const currentTime = new Date();
        const elapsedSeconds = Math.floor((currentTime - startTime) / 1000);
        const minutes = Math.floor(elapsedSeconds / 60);
        const seconds = elapsedSeconds % 60;
        const formattedTime = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

        document.getElementById('timer').innerText = `Czas: ${formattedTime}`;
        setTimeout(updateTimer, 1000); // Aktualizacja co sekundę
    }
        function updateScore(winner) {
        if (winner === 'X') {
            xWins++;
        } else if (winner === 'O') {
            oWins++;
        }

        document.getElementById('xWins').innerText = xWins;
        document.getElementById('oWins').innerText = oWins;
        document.getElementById('draws').innerText = draws;
    }
</script>

</body>
</html>
