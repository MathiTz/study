package main

import (
	"bufio"
	"encoding/json"
	"fmt"
	"os"
	"regexp"
	"sort"
	"strings"
)

type MatchSummary struct {
	TotalKills int            `json:"total_kills"`
	Players    []string       `json:"players"`
	Kills      map[string]int `json:"kills"`
}

type DeathCauseReport struct {
	KillsByMeans map[string]int `json:"kills_by_means"`
}

type RankingReport struct {
	Rankings []PlayerRanking `json:"rankings"`
}

type PlayerRanking struct {
	Player string `json:"player"`
	Kills  int    `json:"kills"`
}

func main() {
	file, err := os.Open("quake.log")
	if err != nil {
		fmt.Println("Error opening file:", err)
		return
	}
	defer file.Close()

	matches := make(map[string]*MatchSummary)
	deathCauses := make(map[string]*DeathCauseReport)
	currentMatch := ""
	matchCount := 0

	scanner := bufio.NewScanner(file)
	killPattern := regexp.MustCompile(`Kill: \d+ \d+ \d+: (.+) killed (.+) by (.+)`)

	for scanner.Scan() {
		line := scanner.Text()
		if strings.Contains(line, "InitGame") {
			matchCount++
			currentMatch = fmt.Sprintf("game_%d", matchCount)
			matches[currentMatch] = &MatchSummary{
				Kills: make(map[string]int),
			}
			deathCauses[currentMatch] = &DeathCauseReport{
				KillsByMeans: make(map[string]int),
			}
		}

		if currentMatch != "" {
			if match := killPattern.FindStringSubmatch(line); match != nil {
				killer := match[1]
				victim := match[2]
				means := match[3]

				matches[currentMatch].TotalKills++
				deathCauses[currentMatch].KillsByMeans[means]++

				if killer != "<world>" {
					matches[currentMatch].Players = appendIfMissing(matches[currentMatch].Players, killer)
					matches[currentMatch].Kills[killer]++
				}

				matches[currentMatch].Players = appendIfMissing(matches[currentMatch].Players, victim)
				if killer == "<world>" {
					matches[currentMatch].Kills[victim]--
				}
			}
		}
	}

	if err := scanner.Err(); err != nil {
		fmt.Println("Error reading file:", err)
		return
	}

	// Generate ranking report
	rankings := generateRankings(matches)

	// Print JSON outputs
	printJSON(matches, "Match Summary")
	printJSON(deathCauses, "Death Cause Report")
	printJSON(rankings, "Ranking Report")
}

func appendIfMissing(slice []string, s string) []string {
	for _, item := range slice {
		if item == s {
			return slice
		}
	}
	return append(slice, s)
}

func generateRankings(matches map[string]*MatchSummary) RankingReport {
	playerKills := make(map[string]int)
	for _, match := range matches {
		for player, kills := range match.Kills {
			playerKills[player] += kills
		}
	}

	var rankings []PlayerRanking
	for player, kills := range playerKills {
		rankings = append(rankings, PlayerRanking{Player: player, Kills: kills})
	}

	sort.Slice(rankings, func(i, j int) bool {
		return rankings[i].Kills > rankings[j].Kills
	})

	return RankingReport{Rankings: rankings}
}

func printJSON(data interface{}, title string) {
	jsonOutput, err := json.MarshalIndent(data, "", "  ")
	if err != nil {
		fmt.Println("Error generating JSON:", err)
		return
	}
	fmt.Printf("%s:\n%s\n\n", title, string(jsonOutput))
}
