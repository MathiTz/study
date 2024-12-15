import {
  FormControl,
  FormErrorMessage,
  FormLabel,
  Input as ChakraInput,
  InputProps as ChakraInputProps,
} from "@chakra-ui/react";

import { FieldError, UseFormRegisterReturn } from "react-hook-form";

interface InputProps extends ChakraInputProps {
  name: string;
  label?: string;
  register: UseFormRegisterReturn;
  error?: FieldError;
}

export default function Input({
  name,
  label,
  error = null,
  register,
  ...rest
}: InputProps) {
  return (
    <FormControl isInvalid={!!error}>
      {!!label && <FormLabel htmlFor={name}>{label}</FormLabel>}

      <ChakraInput
        focusBorderColor="pink.500"
        name={name}
        id={name}
        bgColor="gray.900"
        variant="filled"
        {...register}
        _hover={{
          bgColor: "gray.900",
        }}
        size="lg"
        {...rest}
      />
      {!!error && <FormErrorMessage>{error.message}</FormErrorMessage>}
    </FormControl>
  );
}

// Example on how to forward refs
// const InputBase: ForwardRefRenderFunction<HTMLInputElement, InputProps> = ({
//   name,
//   label,
//   ...rest
// }) => {
//   return (
//     <FormControl>
//       {!!label && <FormLabel htmlFor={name}>{label}</FormLabel>}
//       <ChakraInput
//         focusBorderColor="pink.500"
//         name={name}
//         id={name}
//         bgColor="gray.900"
//         variant="filled"
//         _hover={{
//           bgColor: "gray.900",
//         }}
//         size="lg"
//         {...rest}
//       />
//     </FormControl>
//   );
// };

// const Input = forwardRef(InputBase);

// export default Input;
