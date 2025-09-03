import { useToast } from 'vue-toastification'

const toast = useToast()

export const useAppToast = () => {
  const showSuccess = (message: string) => {
    toast.success(message, {
      position: 'top-right',
      timeout: 3000,
      closeOnClick: true,
      pauseOnHover: true,
    })
  }

  const showError = (message: string) => {
    toast.error(message, {
      position: 'top-right',
      timeout: 5000,
      closeOnClick: true,
      pauseOnHover: true,
    })
  }

  const showWarning = (message: string) => {
    toast.warning(message, {
      position: 'top-right',
      timeout: 4000,
      closeOnClick: true,
      pauseOnHover: true,
    })
  }

  return {
    showSuccess,
    showError,
    showWarning,
  }
}