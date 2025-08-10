import { TeamUser } from "@/types/team-user";

export interface Team {
  id: number;
  name: string;
  owner?: TeamUser;
  users: TeamUser[];
  role: string;
  is_current: boolean;
  created_at: string;
  updated_at: string;

  [key: string]: unknown;
}
