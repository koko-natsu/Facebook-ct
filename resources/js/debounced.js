import { customRef } from 'vue'

export function useDebouncedRef(store, value, delay = 200) {
  let timeout
  return customRef((track, trigger) => {
    return {
      get() {
        track()
        return store.getters.postMessage;
      },
      set(postMessage) {
        clearTimeout(timeout)
        timeout = setTimeout(() => {
          value = postMessage
          trigger()
          store.commit('updateMessage', postMessage)
        }, delay)
      }
    }
  })
}

