package main

import (
	"fmt"
	"io"
	"net/http"
	"regexp"
)

// Google I/O 2012 - Go Concurrency Patterns
// https://www.youtube.com/watch?v=f6kdp27TYZs
// <-chan - canal somente-leitura
// chan<- - canal somente-escrita
func titulo(urls ...string) <-chan string {
	c := make(chan string)
	for _, url := range urls {
		go func(url string) {
			resp, _ := http.Get(url)
			html, _ := io.ReadAll(resp.Body)

			r, _ := regexp.Compile(`<title>(.*?)</title>`)
			c <- r.FindStringSubmatch(string(html))[1]
		}(url)
	}

	return c
}

func main() {
	t1 := titulo("https://www.cod3r.com.br", "https://www.google.com")
	t2 := titulo("https://www.amazon.com.br", "https://www.youtube.com")

	fmt.Println("Primeiros: ", <-t1, "|", <-t2)
	fmt.Println("Segundos: ", <-t1, "|", <-t2)
}
