import { Team } from "@/types/team";

export interface User {
  id: number;
  name: string;
  email: string;
  avatar?: string;
  email_verified_at: string | null;
  created_at: string;
  updated_at: string;
  timezone: string;
teams?: Team[];
role: string;
  [key: string]: unknown;
}
