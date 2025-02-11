package main

import (
	"encoding/json"
	"fmt"
	"io"
	"net/http"
	"sync"
	"time"
)

type timer struct {
	start time.Time
}

func ElapsedTime(id string) func() {
	start := time.Now()

	return func() {
		fmt.Printf("Elapsed time %q: %s\n", id, time.Since(start))
	}
}

type User struct {
	ID int `json:"id"`
}

type Post struct {
	ID int `json:"id"`
}

type Result struct {
	PostsIds []int `json:"postIds"`
	UserId   int   `json:"userId"`
}

func GetUsers(param string) []User {
	var users []User
	resp, err := http.Get(fmt.Sprintf("https://jsonplaceholder.typicode.com/%s", param))
	if err != nil {
		panic(err)
	}

	defer resp.Body.Close()

	err = json.NewDecoder(resp.Body).Decode(&users)
	if err != nil {
		panic(err)
	}

	return users
}

func GetPosts(param string, userId string) []Post {
	var posts []Post
	resp, err := http.Get(fmt.Sprintf("https://jsonplaceholder.typicode.com/users/%s/%s", userId, param))
	if err != nil {
		panic(err)
	}

	defer resp.Body.Close()

	err = json.NewDecoder(resp.Body).Decode(&posts)
	if err != nil {
		panic(err)
	}

	return posts
}

func main() {
	defer ElapsedTime("Original")()
	//var client = &http.Client{}
	//req, err := http.NewRequest(http.MethodGet, "https://jsonplaceholder.typicode.com/users", nil)
	//if err != nil {
	//	fmt.Printf("error: %s", err)
	//}
	//
	//resp, err := client.Do(req)
	//if err != nil {
	//	panic(err)
	//}
	//
	//defer resp.Body.Close()
	//
	//if resp.StatusCode != http.StatusOK {
	//	fmt.Printf("error: %s", err)
	//}

	users := GetUsers("users")
	// Importante: json.NewDecoder(resp.Body).Decode(&users) -> json.Unmarshal([]byte, &users)
	// Teste unitário
	// Linter - errcheck / inefassign
	// Gomock - Testify (https://github.com/stretchr/testify)
	//err = json.NewDecoder(resp.Body).Decode(&users)
	//if err != nil {
	//	fmt.Printf("error: %s", err)
	//}

	var wg sync.WaitGroup
	wg.Add(len(users))

	var result []Result
	//result := make([]map[string]interface{}, len(users))
	//result := make([]map[string]interface{}, 0, len(users))

	// Array x Slice
	// Slice[:], Slice [x:], Slice [:x], Slice[x:y]
	// {1, 2, 3, 4, 5} -> Slice[1:3] -> {2, 3}
	for _, user := range users {
		//wg.Add(1)
		go func() {
			defer wg.Done()

			postsUrl := fmt.Sprintf("https://jsonplaceholder.typicode.com/users/%d/posts", user.ID)

			r1, err := http.Get(postsUrl)
			if err != nil {
				fmt.Printf("error: %s", err)
			}

			posts := GetPosts("posts", string(rune(user.ID)))
			bytes, _ := io.ReadAll(r1.Body)
			json.Unmarshal(bytes, &posts)

			defer r1.Body.Close()

			var userPostIds []int

			for _, post := range posts {
				userPostIds = append(userPostIds, post.ID)
			}
			result = append(result, Result{PostsIds: userPostIds, UserId: user.ID})
		}()
	}
	wg.Wait()

	// read result as json
	jsonResult, err := json.Marshal(result)
	if err != nil {
		fmt.Printf("error: %s", err)
	}

	fmt.Println(string(jsonResult))
}
