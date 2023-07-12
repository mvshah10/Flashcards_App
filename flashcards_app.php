<?php
session_start();

// Connect to your database
$dbHost = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'flashcards';

$conn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbName);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch flashcard data from the database
$sql = "SELECT * FROM flashcards";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $flashcards = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $flashcards = [];
}

// Close database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Flashcards</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        
        .flashcard-container { 
            font-weight: bold;
            width: 300px;
            height: 200px;
            perspective: 800px;
            position: relative;
        }

        .flashcard {
            width: 100%;
            height: 100%;
            position: absolute;
            transform-style: preserve-3d;
            transition: transform 0.6s;
        }

        .front, .back {
            width: 100%;
            height: 100%;
            position: absolute;
            backface-visibility: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 18px;
        }

        .front {
            background-color: lightskyblue;
            transform: rotateY(0);
        }

        .back {
            background-color: lightgrey;
            transform: rotateY(180deg);
        }

        .flashcard-container.flipped .flashcard {
            transform: rotateY(180deg);
        }

        .flashcard:hover {
            cursor: pointer;
        }

        #next-btn {
            margin-top: 15px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        var flashcards = <?php echo json_encode($flashcards); ?>;
        var currentPosition = <?php echo isset($_SESSION['position']) ? $_SESSION['position'] : 0; ?>;
        
        function loadFlashcard(position) {
            var flashcard = flashcards[position];
            var question = flashcard.question;
            var answer = flashcard.answer;
            
            $("#flashcard-container .front").text(question);
            $("#flashcard-container .back").text(answer);
            
            $("#flashcard-container").removeClass("flipped");
        }
        
        function flipCard() {
            $("#flashcard-container").toggleClass("flipped");
        }
        
        $(document).ready(function() {
            loadFlashcard(currentPosition);
            
            $("#next-btn").click(function() {
                currentPosition++;
                
                if (currentPosition >= flashcards.length) {
                    currentPosition = 0;
                }
                
                loadFlashcard(currentPosition);
            });
            
            $("#flashcard-container").click(function() {
                flipCard();
            });
        });
    </script>
</head>
<body>
    <div id="flashcard-container" class="flashcard-container">
        <div class="flashcard">
            <div class="front"></div>
            <div class="back"></div>
        </div>
    </div>
    <button id="next-btn">Next</button>
</body>
</html>
