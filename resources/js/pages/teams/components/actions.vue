<script setup lang="ts">
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import { MoreVerticalIcon } from 'lucide-vue-next';
import { Team } from '@/types/team';
import DeleteTeam from '@/pages/teams/components/delete-team.vue';
import Users from '@/pages/teams/components/users.vue';
import TeamForm from '@/pages/teams/components/team-form.vue';
import LeaveTeam from '@/pages/teams/components/leave-team.vue';

const props = defineProps<{
  team: Team;
}>();
</script>

<template>
  <DropdownMenu modal>
    <DropdownMenuTrigger as-child>
      <Button variant="ghost" class="size-8">
        <MoreVerticalIcon />
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent align="end">
      <Users :team="team" v-if="team.role === 'owner'">
        <DropdownMenuItem @select="(e) => e.preventDefault()">Users</DropdownMenuItem>
      </Users>
      <TeamForm :team="team" v-if="['owner', 'admin'].includes(team.role)">
        <DropdownMenuItem @select="(e) => e.preventDefault()">Edit</DropdownMenuItem>
      </TeamForm>
      <LeaveTeam :team="team" v-if="team.role !== 'owner'">
        <DropdownMenuItem @select="(e) => e.preventDefault()">Leave Team</DropdownMenuItem>
      </LeaveTeam>
      <template v-if="team.role === 'owner'">
        <DropdownMenuSeparator />
        <DeleteTeam :team="props.team">
          <DropdownMenuItem variant="destructive" @select="(e) => e.preventDefault()">Delete</DropdownMenuItem>
        </DeleteTeam>
      </template>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
