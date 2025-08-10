import { ColumnDef } from '@tanstack/vue-table';
import { TeamUser } from '@/types/team-user';
import { h } from 'vue';
import { Badge } from '@/components/ui/badge';
import InvitationActions from '@/pages/teams/components/invitation-actions.vue';

export const invitationColumns: ColumnDef<TeamUser>[] = [
  {
    accessorKey: 'team_name',
    header: 'Team name',
    enableColumnFilter: true,
    enableSorting: true,
  },
  {
    accessorKey: 'role',
    header: 'Role',
    enableColumnFilter: true,
    enableSorting: true,
    cell: ({ row }) => {
      return h(Badge, { variant: 'outline' }, () => row.original.role);
    },
  },
  {
    id: 'actions',
    enableColumnFilter: false,
    enableSorting: false,
    cell: ({ row }) => {
      return h(InvitationActions, { invitation: row.original });
    },
  },
];
