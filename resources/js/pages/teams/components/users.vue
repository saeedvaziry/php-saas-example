<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { TeamUser } from '@/types/team-user';
import { ColumnDef } from '@tanstack/vue-table';
import { h } from 'vue';
import { Sheet, SheetClose, SheetContent, SheetDescription, SheetFooter, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import DataTable from '@/components/data-table.vue';
import { Button } from '@/components/ui/button';
import Invite from '@/pages/teams/components/invite.vue';
import { Team } from '@/types/team';
import UsersActions from '@/pages/teams/components/users-actions.vue';

const columns: ColumnDef<TeamUser>[] = [
  {
    accessorKey: 'email',
    header: 'Email',
    enableColumnFilter: true,
    enableSorting: true,
  },
  {
    id: 'role',
    header: 'Role',
    enableColumnFilter: false,
    enableSorting: false,
    cell: ({ row }) => h(Badge, { variant: 'outline' }, () => row.original.role),
  },
  {
    id: 'status',
    header: 'Status',
    enableColumnFilter: false,
    enableSorting: false,
    cell: ({ row }) => h(Badge, { variant: 'outline' }, () => (row.original.type === 'user' ? 'registered' : 'invited')),
  },
  {
    id: 'actions',
    enableColumnFilter: false,
    enableSorting: false,
    cell: ({ row }) =>
      h(UsersActions, {
        teamId: row.original.team_id,
        user: row.original,
      }),
  },
];

const props = defineProps<{
  team: Team;
}>();
</script>

<template>
  <Sheet>
    <SheetTrigger as-child>
      <slot />
    </SheetTrigger>
    <SheetContent class="sm:max-w-2xl">
      <SheetHeader>
        <SheetTitle>Team users</SheetTitle>
        <SheetDescription>Here you can manage team users</SheetDescription>
      </SheetHeader>
      <div class="p-4">
        <DataTable :columns="columns" :data="[...(props.team.owner ? [props.team.owner] : []), ...(props.team.users || [])]" />
      </div>
      <SheetFooter>
        <div class="flex items-center gap-2">
          <SheetClose as-child>
            <Button variant="outline">Close</Button>
          </SheetClose>
          <Invite :team="props.team">
            <Button>Invite</Button>
          </Invite>
        </div>
      </SheetFooter>
    </SheetContent>
  </Sheet>
</template>
