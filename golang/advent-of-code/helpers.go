package advent_of_code

import (
	"os"
	"regexp"
	"strconv"
	"strings"
)

const (
	BracketPositionLeft  = "left"
	BracketPositionRight = "right"
)

func CheckError(e error) {
	if e != nil {
		panic(e)
	}
}

func SumArray(numbers []int) int {
	result := 0
	for i := 0; i < len(numbers); i++ {
		result += numbers[i]
	}
	return result
}

func GetPuzzleList(entry string) (string, []string) {
	data, err := os.ReadFile(entry)
	CheckError(err)

	return string(data), strings.Split(string(data), "\n")
}

func OnlyDecreasing(entry []int) bool {
	tolerance := 0

	for i := 1; i < len(entry); i++ {
		if entry[i] > entry[i-1] || entry[i] == entry[i-1] {
			if tolerance == 0 {
				tolerance++
				continue
			}

			return false
		}
	}

	return true
}

func OnlyIncreasing(entry []int) bool {
	tolerance := 0
	for i := 1; i < len(entry); i++ {
		if entry[i] < entry[i-1] || entry[i] == entry[i-1] {
			if tolerance == 0 {
				tolerance++
				continue
			}

			return false
		}
	}
	return true
}

func MaxDiff(input []int, max int) bool {
	tolerance := 0

	for i := 1; i < len(input); i++ {
		if absValue(input[i]-input[i-1]) > max {
			if tolerance == 0 {
				tolerance++
				continue
			}

			return false
		}
	}
	return true
}

func MinDiff(input []int, min int) bool {
	tolerance := 0

	for i := 1; i < len(input); i++ {
		if absValue(input[i]-input[i-1]) < min {
			if tolerance == 0 {
				tolerance++
				continue
			}

			return false
		}
	}
	return true
}

func absValue(i int) int {
	if i < 0 {
		return i * -1
	}

	return i
}

func removeBracket(entry string, position string) int {
	switch position {
	case BracketPositionLeft:
		entryNum, _ := strconv.Atoi(strings.TrimLeft(entry, "("))
		return entryNum
	case BracketPositionRight:
		entryNum, _ := strconv.Atoi(strings.TrimRight(entry, ")"))
		return entryNum
	}

	return 0
}

func CleanStringToInt(entry string, replace string) int {
	nums := strings.Split(entry, replace)

	return removeBracket(nums[0], BracketPositionLeft) * removeBracket(nums[1], BracketPositionRight)
}

func FindByRegex(entry string, regex string) [][]string {
	r := regexp.MustCompile(regex)
	return r.FindAllStringSubmatch(entry, -1)
}

func ReplaceByRegex(entry string, regex string, replace string) string {
	r := regexp.MustCompile(regex)
	return r.ReplaceAllString(entry, replace)
}
