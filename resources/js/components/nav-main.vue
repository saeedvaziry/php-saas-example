<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import TeamSwitch from '@/components/team-switch.vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { getInitials } from '@/composables/use-initials';
import { ChevronsUpDownIcon } from 'lucide-vue-next';

defineProps<{
  items: NavItem[];
}>();

const page = usePage<SharedData>();
</script>

<template>
  <SidebarGroup class="px-2 py-0">
    <SidebarGroupLabel>Platform</SidebarGroupLabel>
    <SidebarMenu>
<SidebarMenuItem>
        <TeamSwitch>
          <SidebarMenuButton
            asChild
            tooltip="Switch Team"
            class="w-full group-data-[collapsible=icon]:flex group-data-[collapsible=icon]:items-center group-data-[collapsible=icon]:justify-center"
          >
            <div class="flex items-center justify-between px-1.5">
              <div class="flex items-center justify-center gap-1.5">
                <Avatar class="size-5 rounded-xs group-data-[collapsible=icon]:size-8">
                  <AvatarFallback class="rounded-sm">
                    {{ getInitials(page.props.team_provider.current?.name.replaceAll(' ', '') ?? 'Select team') }}
                  </AvatarFallback>
                </Avatar>
                <span class="group-data-[collapsible=icon]:hidden">{{ page.props.team_provider.current?.name || 'Select team' }}</span>
              </div>
              <ChevronsUpDownIcon class="size-5 group-data-[collapsible=icon]:hidden" />
            </div>
          </SidebarMenuButton>
        </TeamSwitch>
      </SidebarMenuItem>
<SidebarMenuItem v-for="item in items" :key="item.title">
        <SidebarMenuButton as-child :is-active="item.href === page.url" :tooltip="item.title">
          <Link :href="item.href">
            <component :is="item.icon" />
            <span>{{ item.title }}</span>
          </Link>
        </SidebarMenuButton>
      </SidebarMenuItem>
    </SidebarMenu>
  </SidebarGroup>
</template>
