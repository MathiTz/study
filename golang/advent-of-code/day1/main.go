package main

import (
	"fmt"
	helpers "github.com/mathitz/advent-of-code"
	"sort"
	"strconv"
	"strings"
)

func main() {
	_, parsedData := helpers.GetPuzzleList("list.txt")

	var firstColumnPair []int
	var secondColumnPair []int

	for _, v := range parsedData {
		locations := strings.Split(v, "  ")

		if locations[0] == "" {
			fmt.Println("First column invalid value")
			continue
		}

		if locations[1] == "" {
			fmt.Println("Second column invalid value")
			continue
		}

		firstColumnInt, err := strconv.Atoi(locations[0])
		helpers.CheckError(err)

		secondColumnInt, err := strconv.Atoi(strings.TrimSpace(locations[1]))
		helpers.CheckError(err)

		firstColumnPair = append(firstColumnPair, firstColumnInt)
		secondColumnPair = append(secondColumnPair, secondColumnInt)
	}

	sort.Ints(firstColumnPair[:])
	sort.Ints(secondColumnPair[:])

	var distance []int
	for i, v := range secondColumnPair {
		calculatedDistance := v - firstColumnPair[i]

		if calculatedDistance < 0 {
			calculatedDistance = calculatedDistance * (-1)
		}

		distance = append(distance, calculatedDistance)
	}

	rightMap := make(map[int]int)

	for _, num := range secondColumnPair {
		rightMap[num]++
	}

	similarityScore := 0

	for _, num := range firstColumnPair {
		similarityScore += num * (rightMap[num])
	}

	fmt.Printf("Day 1 - Challenge 1: %v\n", helpers.SumArray(distance))
	fmt.Printf("Day 1 - Challenge 2: %v", similarityScore)
}
