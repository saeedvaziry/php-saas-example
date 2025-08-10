export interface TeamUser {
  id: number;
  team_id: number;
  team_name: string;
  email: string;
  role: string;
  type: "user" | "invitation";
}
