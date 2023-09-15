package arquitetura

import (
	"runtime"
	"testing"
)

func TestDependente(t *testing.T) {
	t.Parallel()
	if runtime.GOARCH == "arm64" {
		t.Skip("Não funciona em arquitetura arm64")
	}

	// ...
	t.Fail()
}
