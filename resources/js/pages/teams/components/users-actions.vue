<script setup lang="ts">
import { TeamUser } from '@/types/team-user';
import RemoveUser from '@/pages/teams/components/remove-user.vue';
import { TrashIcon } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

const props = defineProps<{
  teamId: number;
  user: TeamUser;
}>();
</script>

<template>
  <RemoveUser :user="props.user" :team-id="teamId">
    <Button variant="outline" class="size-7">
      <TrashIcon class="size-3"/>
    </Button>
  </RemoveUser>
</template>
