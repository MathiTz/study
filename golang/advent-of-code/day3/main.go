package main

import (
	"fmt"
	helpers "github.com/mathitz/advent-of-code"
	"strings"
)

func main() {
	_, input := helpers.GetPuzzleList("list.txt")
	sum := 0

	// Part 1

	for _, value := range input {
		subString := helpers.FindByRegex(value, `(mul\()(\d{1,3}),([0-9]{1,3})\)`)

		for _, sub := range subString {
			subNums := helpers.ReplaceByRegex(sub[0], `mul`, "")

			sum += helpers.CleanStringToInt(subNums, ",")
		}
	}

	fmt.Printf("Part 1: %v\n", sum)

	// Part 2

	sum = 0
	active := true

	for _, sub := range input {
		subNums := helpers.FindByRegex(sub, `((mul\()([\d]{1,3})[,]([0-9]{1,3})\))|(do\(\))|(don\'t\(\))`)

		for _, sub := range subNums {
			if strings.Contains(sub[0], "don") {
				active = false
				continue
			} else if strings.Contains(sub[0], "do") {
				active = true
				continue
			} else {
				if active {
					subNums := helpers.ReplaceByRegex(sub[0], `mul`, "")

					sum += helpers.CleanStringToInt(subNums, ",")
				}
			}
		}
	}

	fmt.Printf("Part 2: %v\n", sum)
}
