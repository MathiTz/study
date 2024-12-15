const [isEnabled, enable] = useReducer(() => true, false);
const [on, toggle] = useReducer((s) => !s, false);

type Menu = null | "file" | "edit" | "view";

const [openMenu, tap] = useReducer((current: Menu, action: Menu) => {
  if (action === current) {
    return null;
  }

  return action;
}, null);

tap("file");
tap(null);
tap("edit");
tap("view");
