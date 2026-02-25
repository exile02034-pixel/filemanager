import folders from '@/routes/user/folders'
import { useForm } from '@inertiajs/vue3'

export const useFolderUser = () => {
  const form = useForm({
    name: '',
    parent_id: null as string | null,
    action: null as 'overwrite' | 'version' | null,
  })

  const createFolder = (onSuccess?: () => void, onError?: (error: any) => void) => {
    form.post(folders.store.url(), {
      preserveScroll: true,
      onSuccess: () => {
        if (onSuccess) onSuccess()
        form.reset()
        form.action = null
      },
      onError: (errors: any) => {
        // Pass the errors up to the component to handle
        if (onError) onError(errors)
      },
    })
  }

  const getFolders = (options?: { search?: string; sort?: string }) => {
    return folders.index.url({ query: options })
  }

  return { form, createFolder, getFolders }
}