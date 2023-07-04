import { Flex, Box, Avatar, Text } from "@chakra-ui/react";

interface ProfileProps {
  showProfileDate?: boolean;
}

export function Profile({ showProfileDate }: ProfileProps) {
  return (
    <Flex align="center">
      {showProfileDate && (
        <Box mr="4" textAlign="right">
          <Text>Matheus Alves</Text>
          <Text color="gray.300" fontSize="small">
            matheusalves789@gmail.com
          </Text>
        </Box>
      )}

      <Avatar
        size="md"
        name="Matheus Alves"
        src="https://github.com/mathitz.png"
      />
    </Flex>
  );
}
