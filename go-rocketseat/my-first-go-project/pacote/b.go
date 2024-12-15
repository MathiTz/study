package pacote

import (
	"fmt"
	"myFirstGoProject/pacote/internal/foo"
)

var Bar string = "hello, Bar!"

func PrintMinha() {
	fmt.Println(foo.Minha)
}
