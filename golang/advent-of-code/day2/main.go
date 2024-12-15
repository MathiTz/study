//package main
//
//import (
//	"fmt"
//	helpers "github.com/mathitz/advent-of-code"
//	"strconv"
//	"strings"
//)
//
//func main() {
//	inputPuzzle := helpers.GetPuzzleList("list.txt")
//	safeReports := 0
//
//	for _, reportNum := range inputPuzzle {
//		numReports := strings.Fields(reportNum)
//		levels := make([]int, len(numReports))
//
//		for i, report := range numReports {
//			num, _ := strconv.Atoi(report)
//			levels[i] = num
//		}
//
//		if len(levels) == 0 {
//			continue
//		}
//
//		decreasing := helpers.OnlyDecreasing(levels)
//		increasing := helpers.OnlyIncreasing(levels)
//		maxDiff := helpers.MaxDiff(levels, 3)
//		minDiff := helpers.MinDiff(levels, 1)
//
//		if (decreasing || increasing) && maxDiff && minDiff {
//			safeReports++
//		}
//	}
//
//	fmt.Println(safeReports)
//}

package main

import (
	"fmt"
	helpers "github.com/mathitz/advent-of-code"
	"strconv"
	"strings"
)

func main() {
	_, input := helpers.GetPuzzleList("list.txt")

	getTotalSafeReportCount(input)
}

func isReportSafe(reportNum []int) bool {
	flagIncrease, flagDecrease := false, false

	for i := 1; i < len(reportNum); i++ {
		diff := reportNum[i] - reportNum[i-1]

		if diff > 0 {
			flagIncrease = true
		} else if diff < 0 {
			flagDecrease = true
		} else {
			return false
		}

		if flagDecrease && flagIncrease {
			return false
		}

		if diff > 3 || diff < -3 {
			return false
		}
	}

	return true
}

func checkReportSafetyWithDeletion(reportNum []int) bool {

	for i := 0; i < len(reportNum); i++ {
		isSafe := isReportSafeWithDeletion(reportNum, i)
		if isSafe {
			return true
		}
	}

	return false
}

func isReportSafeWithDeletion(report []int, deleteIndex int) bool {
	copyReport := make([]int, len(report))
	copy(copyReport, report)

	if deleteIndex == len(copyReport)-1 {
		copyReport = copyReport[:deleteIndex]
	} else {
		copyReport = append(copyReport[:deleteIndex], copyReport[deleteIndex+1:]...)
	}
	return isReportSafe(copyReport)
}

func getTotalSafeReportCount(reports []string) int {
	var count int
	var countWithDeletion int
	for _, report := range reports {
		numReports := strings.Fields(report)
		levels := make([]int, len(numReports))

		for i, report := range numReports {
			num, _ := strconv.Atoi(report)
			levels[i] = num
		}

		if isReportSafe(levels) {
			count++
		} else if checkReportSafetyWithDeletion(levels) {
			countWithDeletion++
		}
	}
	fmt.Printf("answer for part 1: %d\nanswer for part 2: %d\n", count, count+countWithDeletion)
	return count
}
