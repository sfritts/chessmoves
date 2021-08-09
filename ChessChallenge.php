<?php

/**
 * @package Chess Coding Challenge
 *
 * @description - ISSIO Solution – Chess board/piece movements Coding Challenge.
 *
 * Chess is a very popular game, and almost everybody knows at least the basic moves of
 * pieces on the board. You don’t have to be a grandmaster to be able to code in this challenge.
 *
 * All we are doing is just implementing the moves of two pieces: Bishop and Rook.
 *
 * Bishops can move diagonally on the board in any direction. Rooks can move only vertically
 * or only horizontally.
 *
 * You can review the rules and the moves here: https://en.wikipedia.org/wiki/Chess#Movement
 *
 * You will be provided with the sample code that creates the table and sets up some pieces
 * (black and white) as shown on the diagram:
 *
 * Your goal is to implement the move of the Bishop (E3) and the Rook (E6). For example:
 * > $board->makeMove(['c', 3], ['e', 1])
 *
 * You take a piece in the square C3 and try to move it to E1. If the chess piece from C3 square
 * can make a successful move to E1, the method will return true. In the move is illegal, it should
 * indicate so. You will have to take into consideration all legal and illegal moves.
 *
 * Feel free to adjust all of the classes and/or adding additional methods.
 *
 * You need to document your code and cover the edge cases, which are listed at the end of the
 * “ChessChallenge.php” file. You should not spend more than 1-2 hours on the exercise.
 *
 * Your performance will be measured on your ability to work with the existing code, follow the
 * instructions, understanding the principles and concepts of Object-Oriented Programming.
 *
 * @version 1.0
 * @author Issio Solutions
 * @copyright Copyright (c) 2020, Issio Solutions, Inc
 */

/**
 * Class Square
 * @description - Contains all the logic and the data of the single square.
 * Also contains the chess piece
 */
class Square {

    private $x; // 1 - 8
    private $y; // 1 - 8
    private $color; // light-dark

    /**
     *
     * @var Piece
     */
    private $piece = NULL;

    /**
     * Square constructor.
     * @param string $x - horizontal coordinates [a - h]
     * @param int $y - vertical coordinates [1 - 8]
     * @param string $color - light/dark
     */
    public function __construct($x, $y, $color) {
        $this->x = $x;
        $this->y = $y;
        $this->color = $color;
    }

    /**
     * @param Piece $piece - chess piece on the square
     */
    public function setPiece($piece) {
        $this->piece = $piece;
    }

    /**
     * @return Piece|null
     */
    public function getPiece() {
        return $this->piece;
    }

    public function getX() {
        return $this->x;
    }

    public function getY() {
        return $this->y;
    }

}

/**
 * Class Board
 * @description - Main Board setup and logic including the moves
 */
class Board {

    static $letters = ['', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
    static $width = 8;
    static $height = 8;
    // $squares grid in the format square = [][], so it could be easily
    // accessed as squares['a'][3];
    private $squares = [];

    /**
     * Turns output on and off for move decisions. Good for debugging.
     *
     * @param boolean $verbose DEFAULT: FALSE
     */
    private $verbose;

    /**
     * Board constructor.
     * 
     * @param boolean $verbose Show output messages.
     */
    public function __construct($verbose = FALSE) {

        $switch = true;

        /**
         * The original spec said to only return TRUE or FALSE. Allow user to turn
         *   output on and off.
         */
        $this->verbose = $verbose;

        // Setting vertical
        for ($i = 1; $i <= self::$width; $i++) {
            $l = self::$letters[$i];
            $this->squares[$l] = [];

            // Setting horizontal
            for ($j = 1; $j <= self::$height; $j++) {
                $color = $switch ? 'dark' : 'light';

                $this->squares[$l][$j] = new Square($l, $j, $color);
                $switch = !$switch;
            }
        }
    }

    /**
     * getSquare - returns the selected square
     * @param string $x - horizontal coordinates [a - h]
     * @param int $y - vertical coordinates [1 - 8]
     * @return Square - returns a square
     */
    public function getSquare($x, $y) {
        return $this->squares[$x][$y];
    }

    /**
     * setPiece - setting a chess piece on the board
     * @param Piece $piece - chess piece on the board
     * @param string $x - horizontal coordinates [a - h]
     * @param int $y - vertical coordinates [1 - 8]
     */
    public function setPiece($piece, $x, $y) {
        $square = $this->getSquare($x, $y);

        $square->setPiece($piece);
        $this->setSquare($square, $x, $y);
    }

    /**
     * setSquare - setting a piece on the board
     * @param Square $square - Square
     * @param string $x - horizontal coordinates [a - h]
     * @param int $y - vertical coordinates [1 - 8]
     */
    private function setSquare($square, $x, $y) {
        $this->squares[$x][$y] = $square;
    }

    /**
     * makeMove - The implementation of the move of the piece on the board
     * picking the piece from the starting square (array), and moving to the
     * ending square (array).
     *
     * @param array $start - starting square [x, y]
     * @param array $end - finishing square [x, y]
     * 
     * @version 2021.08.08 - sfritts Stopped looping through entire board to look for empty squares.
     *   Fixed bug: if piece is in the way but is opposite team's color, the move would have been allowed.
     * 
     */
    public function makeMove($start, $end) {
        $movingPiece = $this->squares[$start[0]][$start[1]]->getPiece();
        $output = "";

        if ($movingPiece) {
            $takePiece = FALSE;
            // is the destination square occupied and is it one of your pieces?
            if ($this->squares[$end[0]][$end[1]]->getPiece()) {
                // what color is the piece on the desitnation square?
                if ($this->squares[$end[0]][$end[1]]->getPiece()->getColor() === $movingPiece->getColor()) {
                    echo $this->verbose ? "One of your pieces is in the destination square\n" : "";
                    return FALSE; // one of your pieces is in destination.
                }

                /**
                 * There are a couple of different ways to do this:
                 *   Assigning the name of the piece to take while I have it available now eliminates my having to look
                 *   it up later and $takePiece will still evaluate to "TRUE". 
                 *   I wasn't sure which one would be preferred, so I went for fewest lines of code.
                 */
                $takePiece = $this->squares[$end[0]][$end[1]]->getPiece()->getType();
            }

            // is this a valid desitnation?
            $validSquares = $movingPiece->validSquares($start, $end);

            if (!$validSquares) {
                echo $this->verbose ? "Destination square is not allowed.\n" : "";
                return FALSE;
            } else { // are their pieces in the way?

                /**
                 * Stop looping through entire board, pick out squares from list
                 *   and check for pieces.
                 */
                foreach ($validSquares as $squareCoor) {
                    $square = $this->getSquare(Board::$letters[$squareCoor[0]], $squareCoor[1]);
                    if ($square->getPiece()) { // occupied
                        echo $this->verbose ? "A " . $square->getPiece()->getType() . " is in the way!\n" : "";
                        return FALSE;
                    }
                }
            }

            if ($takePiece) {
                echo $this->verbose ? "Take the $takePiece!\n" : "";
            } else {
                echo $this->verbose ? "Legal Move.\n" : "";
            }

            // remove old piece?
            // set piece?
            return TRUE;
        } else {
            echo $this->verbose ? "No piece on staring square.\n" : "";
            return FALSE;
        }
    }

}

/**
 * Class Piece
 * @todo - Adjust the class appropriately
 * 
 * @version 2021.08.05 sfritts - Added getter methods for color and type properties.
 */
abstract class Piece {

    private $color; // black/white]
    private $type;

    /**
     * Piece constructor.
     * @param string $color - piece color [black/white]
     * @param string $type - piece type, queen, king, pawn etc.
     */
    public function __construct($color, $type) {
        $this->color = $color;
        $this->type = $type;
    }

// SF - decided against this is it could be a hole, making all private properties 
//        accessible. Might not be desired...just wanted you to know I started out this way...
//        
//    public function __get($property) {
//        return $this->$property;
//    }

    /**
     * 
     * @return string Piece type name.
     */
    public function getType() {
        return $this->type;
    }

    /**
     * 
     * @return type Piece color.
     */
    public function getColor() {
        return $this->color;
    }

    /**
     * current existing piece should be making a move to x, y
     *
     * @param   string  $x - horizontal coordinates [a - h]
     * @param   int     $y - vertical coordinates [1 - 8]
     * @return  mixed
     */
    abstract protected function makeMove($x, $y);

    /**
     * Determines is the destination requested is valid and creates a list of squares
     *   you have to go over to get there.
     * 
     * @param   array $start The starting square. [x,y]
     * @param   array $end   The destination square. [x,y]
     * @return  varient FALSE for invalid path. Array of square coordinates you have to go over to get to the destination.
     */
    abstract protected function validSquares($start, $end);
}

/**
 * Class Pawn
 * 
 * @version 2021.08.05 sfritts - Added makeMove method as required by Piece abstract.
 */
class Pawn extends Piece {

    public function __construct($color) {
        parent::__construct($color, 'Pawn');
    }

    function makeMove($x, $y) {
        return;
    }

    function validSquares($start, $end) {
        return;
    }

}

/**
 * Class Bishop
 * @todo - finish the class
 * 
 * how do Bishops move?
 */
class Bishop extends Piece {

    public function __construct($color) {
        parent::__construct($color, 'Bishop');
    }

    function makeMove($x, $y) {
        return;
    }

    function validSquares($start, $end) {
        // we need numeric values for the alphabetic columns
        $startColumnNum = array_search($start[0], Board::$letters);
        $endColumnNum = array_search($end[0], Board::$letters);

        // is the desitnation square at the right angle?
        if ((($endColumnNum - $startColumnNum) == ($end[1] - $start[1])) || ((-$endColumnNum + $startColumnNum) == ($end[1] - $start[1]))) {

            // return a list of the squares we have to go past to get there.
            $pathSquares = [];
            $currentCol = $startColumnNum;
            $currentRow = $start[1];

            /**
             * @todo I feel like this is repetitive...
             */
            if (($startColumnNum < $endColumnNum) && ($start[1] > $end[1])) {
                // down and right
                for ($i = 1; $currentCol < $endColumnNum; $i++) {
                    $currentCol = $startColumnNum + $i;
                    $currentRow = $start[1] - $i;

                    $pathSquares[] = [$currentCol, $currentRow];
                }
            }

            if (($startColumnNum > $endColumnNum) && ($start[1] > $end[1])) {
                // down and left
                for ($i = 1; $currentCol > $endColumnNum; $i++) {
                    $currentCol = $startColumnNum - $i;
                    $currentRow = $start[1] - $i;

                    $pathSquares[] = [$currentCol, $currentRow];
                }
            }

            if (($startColumnNum < $endColumnNum) && ($start[1] < $end[1])) {
                // up and right
                for ($i = 1; $currentCol < $endColumnNum; $i++) {
                    $currentCol = $startColumnNum + $i;
                    $currentRow = $start[1] + $i;

                    $pathSquares[] = [$currentCol, $currentRow];
                }
            }

            if (($startColumnNum > $endColumnNum) && ($start[1] < $end[1])) {
                // up and left
                for ($i = 1; $currentCol > $endColumnNum; $i++) {
                    $currentCol = $startColumnNum - $i;
                    $currentRow = $start[1] + $i;

                    $pathSquares[] = [$currentCol, $currentRow];
                }
            }

            array_pop($pathSquares); // take the destination square off.
            return $pathSquares;
        }

        return FALSE;
    }

}

/**
 * Class Rook
 * @todo - finish the class
 * 
 * how do rooks move?
 */
class Rook extends Piece {

    public function __construct($color) {
        parent::__construct($color, 'Rook');
    }

    /**
     * 
     * @param  string   $x
     * @param  integer  $y
     * @return boolean
     */
    function makeMove($x, $y) {
        return;
    }

    function validSquares($start, $end) {

        if ($start[0] == $end[0] || $start[1] == $end[1]) {
            // is there a piece in the way?
            $currentCol = $startColNum = array_search($start[0], Board::$letters);
            $currentRow = $start[1];
            $endColNum = array_search($end[0], Board::$letters);
            $pathSquares = FALSE;

            /**
             * @todo I feel like this is also repetitive...
             */
            if ($startColNum > $endColNum) {
                // going left
                for ($i = 1; $currentCol > $endColNum; $i++) {
                    $currentCol = $startColNum - $i;
                    $currentRow = $start[1];

                    $pathSquares[] = [$currentCol, $currentRow];
                }
            }
            if ($startColNum < $endColNum) {
                // going right
                for ($i = 1; $currentCol < $endColNum; $i++) {
                    $currentCol = $startColNum + $i;
                    $currentRow = $start[1];

                    $pathSquares[] = [$currentCol, $currentRow];
                }
            }
            if ($start[1] > $end[1]) {
                // going down
                for ($i = 1; $currentRow > $end[1]; $i++) {
                    $currentCol = $startColNum;
                    $currentRow = $start[1] - $i;

                    $pathSquares[] = [$currentCol, $currentRow];
                }
            }
            if ($start[1] < $end[1]) {
                // going up
                for ($i = 1; $currentRow < $end[1]; $i++) {
                    $currentCol = $startColNum;
                    $currentRow = $start[1] + $i;

                    $pathSquares[] = [$currentCol, $currentRow];
                }
            }
            array_pop($pathSquares); // take the destination square off.
            return $pathSquares;
        } else {
            return FALSE;
        }
    }

}

// Setting Up the Board: START
$board = new Board(TRUE);
// White Pawn on B4
$board->setPiece(new Pawn('white'), 'b', 4);
// White Pawn on E4
$board->setPiece(new Pawn('white'), 'e', 4);
// White Bishop on C3
$board->setPiece(new Bishop('white'), 'c', 3);
// Black Pawn on F6
$board->setPiece(new Pawn('black'), 'f', 6);
// Black Rook on E6
$board->setPiece(new Rook('black'), 'e', 6);
// Setting Up the Board: END
// 
// Moves for the White Bishop
// 
// Bishops only move diagonally
echo "----Start Bishop Moves----\n";
$board->makeMove(['c', 3], ['e', 1]); // Success - Legal move
$board->makeMove(['c', 3], ['f', 6]); // Success - Take over the black pawn.

$board->makeMove(['c', 3], ['h', 5]); // Fail - Illegal move bishops in general
$board->makeMove(['c', 3], ['b', 4]); // Fail - Illegal move (white pawn on the way)
$board->makeMove(['c', 3], ['h', 8]); // Fail - Illegal move (cannot jump over other pieces)
echo "----End Bishop Moves----\n";
////
//// Moves for the Black Rook
//// Rooks moving only vertically or horizontally
print("----Start Rook Moves----\n");
$board->makeMove(['e', 6], ['a', 6]); // Success - Legal move
$board->makeMove(['e', 6], ['e', 4]); // Success - Take over the white pawn.

$board->makeMove(['e', 6], ['c', 5]); // Fail - Illegal move rooks in general
$board->makeMove(['e', 6], ['f', 6]); // Fail - Illegal move (black pawn on the way)
$board->makeMove(['e', 6], ['e', 1]); //
print("----End Rook Moves----\n");


