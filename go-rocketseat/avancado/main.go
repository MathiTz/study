package main

import "slices"

func main() {
	slices.Contains()
}

func Contains[T comparable](s []T, cmp T) bool {
	for _, str := range s {
		if str == cmp {
			return true
		}
	}

	return false
}
