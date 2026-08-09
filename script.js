// Get HTML Elements

const choiceButtons = document.querySelectorAll(".choice");

const userScoreElement =
    document.getElementById("user-score");

const computerScoreElement =
    document.getElementById("computer-score");

const computerChoiceElement =
    document.getElementById("computer-choice");

const resultElement =
    document.getElementById("result");

const movesElement =
    document.getElementById("moves");

const resetButton =
    document.getElementById("reset-btn");


// Score Variables

let userScore = 0;
let computerScore = 0;


// Available Options

const options = [
    "stone",
    "paper",
    "scissors"
];


// Computer Random Choice

function getComputerChoice() {

    const randomIndex =
        Math.floor(Math.random() * options.length);

    return options[randomIndex];
}


// Convert Choice Name

function displayName(choice) {

    const names = {

        stone: "🪨 Stone",

        paper: "📄 Paper",

        scissors: "✂️ Scissors"

    };

    return names[choice];
}


// Find Winner

function getWinner(user, computer) {

    // Same choice = Draw

    if (user === computer) {

        return "tie";
    }


    // User winning conditions

    if (
        (user === "stone" && computer === "scissors") ||

        (user === "paper" && computer === "stone") ||

        (user === "scissors" && computer === "paper")
    ) {

        return "user";
    }


    // Otherwise Computer wins

    return "computer";
}


// Play Game

function playGame(userChoice) {

    // Computer automatically selects

    const computerChoice =
        getComputerChoice();


    // Display computer choice

    computerChoiceElement.textContent =
        displayName(computerChoice);


    // Find winner

    const winner =
        getWinner(userChoice, computerChoice);


    // Remove old result colors

    resultElement.classList.remove(
        "win",
        "lose",
        "tie"
    );


    // User Wins

    if (winner === "user") {

        userScore++;

        resultElement.textContent =
            "🎉 You Win!";

        resultElement.classList.add("win");

    }


    // Computer Wins

    else if (winner === "computer") {

        computerScore++;

        resultElement.textContent =
            "😢 Computer Wins!";

        resultElement.classList.add("lose");

    }


    // Draw

    else {

        resultElement.textContent =
            "🤝 It's a Draw!";

        resultElement.classList.add("tie");
    }


    // Update Scores

    userScoreElement.textContent =
        userScore;

    computerScoreElement.textContent =
        computerScore;


    // Display Both Choices

    movesElement.textContent =
        `You: ${displayName(userChoice)}  |  Computer: ${displayName(computerChoice)}`;
}


// Reset Game

function resetGame() {

    userScore = 0;

    computerScore = 0;


    // Reset Score

    userScoreElement.textContent = "0";

    computerScoreElement.textContent = "0";


    // Reset Computer Choice

    computerChoiceElement.textContent = "❓";


    // Reset Result

    resultElement.textContent =
        "Make your choice!";


    resultElement.classList.remove(
        "win",
        "lose",
        "tie"
    );


    // Reset Moves

    movesElement.textContent =
        "You vs Computer";
}


// Add Click Event To Buttons

choiceButtons.forEach(function(button) {

    button.addEventListener("click", function() {

        const userChoice =
            this.getAttribute("data-choice");

        playGame(userChoice);

    });

});


// Reset Button Event

resetButton.addEventListener(
    "click",
    resetGame
);