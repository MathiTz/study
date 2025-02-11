package main

import (
	"github.com/stretchr/testify/assert"
	"testing"
)

func TestElapsedTime(t *testing.T) {
	t.Run("TestElapsedTime", func(t *testing.T) {
		defer ElapsedTime("Original")()
	})
}

func TestGetUsers(t *testing.T) {
	t.Run("TestGetUsers", func(t *testing.T) {
		users := GetUsers("users")

		if len(users) == 0 {
			t.Errorf("Expected users to be greater than 0")
		}

		if users[0].ID != 1 {
			t.Errorf("Expected user ID to be 1")
		}

		assert.Len(t, users, 10)
	})
}
