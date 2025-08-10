<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import TeamForm from '@/pages/teams/components/team-form.vue';
import { PlusIcon } from 'lucide-vue-next';
import { SharedData } from '@/types';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem, DropdownMenuRadioGroup, DropdownMenuRadioItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger
} from '@/components/ui/dropdown-menu';

const page = usePage<SharedData>();
const form = useForm({});
const teams = computed(() => page.props.team_provider.list || []);
const currentTeamId = ref(page.props.team_provider.current?.id?.toString() || '');

function handleTeamChange(teamId: string) {
  const selectedTeam = teams.value.find((p) => p.id.toString() === teamId);
  if (selectedTeam) {
    currentTeamId.value = selectedTeam.id.toString();
    form.put(`/settings/teams/${teamId}/switch?currentPath=${window.location.pathname}`);
  }
}
</script>

<template>
  <div class="flex items-center">
    <DropdownMenu :modal="false">
      <DropdownMenuTrigger as-child>
        <slot />
      </DropdownMenuTrigger>

      <DropdownMenuContent class="w-56" align="start">
        <DropdownMenuRadioGroup v-model="currentTeamId">
          <DropdownMenuRadioItem
            v-for="team in teams"
            :key="team.id.toString()"
            :value="team.id.toString()"
            @select="handleTeamChange(team.id.toString())"
          >
            {{ team.name }}
          </DropdownMenuRadioItem>
        </DropdownMenuRadioGroup>

        <DropdownMenuSeparator />

        <TeamForm>
          <DropdownMenuItem class="gap-0" as-child @select.prevent>
            <div class="flex items-center">
              <PlusIcon :size="16" />
              <span class="ml-2">Create new team</span>
            </div>
          </DropdownMenuItem>
        </TeamForm>
      </DropdownMenuContent>
    </DropdownMenu>
  </div>
</template>
