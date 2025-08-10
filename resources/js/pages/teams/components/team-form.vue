<script setup lang="ts">
import { ref, watch, defineProps } from 'vue';
import { useForm } from '@inertiajs/vue3';
import {
  Dialog,
  DialogTrigger,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
  DialogFooter,
  DialogClose,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import InputError from '@/components/input-error.vue';
import { Label } from '@/components/ui/label';
import { LoaderCircle } from 'lucide-vue-next';
import type { Team } from '@/types/team';

const props = defineProps<{
  team?: Team;
  defaultOpen?: boolean;
}>();

const open = ref(props.defaultOpen ?? false);

watch(() => props.defaultOpen, (val) => {
  if (val !== undefined) open.value = val;
});

const form = useForm({
  name: props.team?.name ?? '',
});

function submit(e?: Event) {
  e?.preventDefault();

  if (props.team) {
    form.put(`/settings/teams/${props.team.id}`, {
      onSuccess: () => (open.value = false),
    });
  } else {
    form.post(`/settings/teams`, {
      onSuccess: () => (open.value = false),
    });
  }
}
</script>

<template>
  <Dialog :open="open" @update:open="open = $event">
    <DialogTrigger as-child>
      <slot />
    </DialogTrigger>

    <DialogContent class="sm:max-w-md">
      <DialogHeader>
        <DialogTitle>{{ props.team ? 'Edit Team' : 'Create Team' }}</DialogTitle>
        <DialogDescription>
          {{ props.team ? 'Edit the team details.' : 'Here you can create a new team.' }}
        </DialogDescription>
      </DialogHeader>

      <form id="team-form" @submit="submit">
        <div class="grid gap-6">
          <div class="grid gap-2">
            <Label for="name">Name</Label>
            <Input
              id="name"
              type="text"
              v-model="form.name"
              name="name"
            />
            <InputError :message="form.errors.name" />
          </div>
        </div>
      </form>

      <DialogFooter>
        <DialogClose as-child>
          <Button type="button" variant="outline">Cancel</Button>
        </DialogClose>
        <Button
          form="team-form"
          type="button"
          :disabled="form.processing"
          @click="submit"
        >
          <LoaderCircle v-if="form.processing" class="animate-spin mr-2" />
          Save
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
