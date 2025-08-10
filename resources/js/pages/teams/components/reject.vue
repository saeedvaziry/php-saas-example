<script setup lang="ts">
import { ref } from 'vue';
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import { TeamUser } from '@/types/team-user';
import { Button } from '@/components/ui/button';
import { useForm } from '@inertiajs/vue3';
import { LoaderCircleIcon } from 'lucide-vue-next';

const props = defineProps<{
  invitation: TeamUser;
}>();
const dialog = ref(false);
const form = useForm({});

const submit = (e: Event) => {
  e.preventDefault();
  form.delete(`/settings/teams/${props.invitation.team_id}/leave`, {
    onSuccess: () => {
      dialog.value = false;
    },
  });
};
</script>

<template>
  <Dialog v-model:open="dialog">
    <DialogTrigger as-child>
      <slot />
    </DialogTrigger>
    <DialogContent>
      <DialogHeader>
        <DialogTitle>Reject {{ props.invitation.team_name }}</DialogTitle>
        <DialogDescription>Reject joining {{ props.invitation.team_name }}</DialogDescription>
      </DialogHeader>

      <p>Are you sure you want to reject joining this team?</p>

      <DialogFooter class="gap-2">
        <DialogClose as-child>
          <Button variant="outline">Cancel</Button>
        </DialogClose>
        <Button @click="submit" variant="destructive" :disabled="form.processing">
          <LoaderCircleIcon v-if="form.processing" class="animate-spin" />
          Reject
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
