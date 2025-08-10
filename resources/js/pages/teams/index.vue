<script setup lang="ts">
import Layout from '@/layouts/settings/layout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { BreadcrumbItem, PaginatedData } from '@/types';
import Container from '@/components/container.vue';
import Heading from '@/components/heading.vue';
import TeamForm from '@/pages/teams/components/team-form.vue';
import { Button } from '@/components/ui/button';
import { PlusIcon } from 'lucide-vue-next';
import { Team } from '@/types/team';
import { TeamUser } from '@/types/team-user';
import DataTable from '@/components/data-table.vue';
import { columns } from '@/pages/teams/components/columns';
import { invitationColumns } from '@/pages/teams/components/invitation-columns';
import { computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Settings',
    href: '/settings',
  },
  {
    title: 'Teams',
    href: '/settings/teams',
  },
];

const page = usePage<{
  teams: PaginatedData<Team>;
  invitations: PaginatedData<TeamUser>;
}>();

const teams = computed(() => page.props.teams);
</script>

<template>
  <Layout :breadcrumbs="breadcrumbs">
    <Head title="Teams" />

    <Container class="max-w-5xl">
      <div class="flex items-start justify-between">
        <Heading title="Teams" description="Here you can manage your teams" />
        <div class="flex items-center gap-2">
          <TeamForm>
            <Button>
              <PlusIcon />
              Create team
            </Button>
          </TeamForm>
        </div>
      </div>

      <DataTable :columns="columns" :paginatedData="teams" />

      <template v-if="page.props.invitations.data.length > 0">
        <Heading title="Invitations" description="Here you can see the teams you're invited to" />
        <DataTable :columns="invitationColumns" :paginatedData="page.props.invitations" />
      </template>
    </Container>
  </Layout>
</template>
