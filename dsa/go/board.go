package main

func Board(board [][]byte, word string) bool {
	for i := 0; i < len(board); i++ {
		for j := 0; j < len(board[0]); j++ {
			cell := board[i][j]
			if cell != word[0] {
				continue
			}

			if walk(i, j, board, word, 0) {
				return true
			}
		}
	}

	return false
}

type point [2]int

var steps = []point{
	{-1, 0},
	{0, 1},
	{1, 0},
	{0, -1},
}

func walk(x, y int, board [][]byte, word string, idx int) bool {
	if idx >= len(word) {
		return true
	}

	if x < 0 || x >= len(board) || y < 0 || y >= len(board[0]) {
		return false
	}

	cell := board[x][y]
	if cell != word[idx] {
		return false
	}

	board[x][y] = '*'

	for _, s := range steps {
		if walk(x+s[0], y+s[1], board, word, idx+1) {
			return true
		}
	}

	board[x][y] = cell

	return false
}
