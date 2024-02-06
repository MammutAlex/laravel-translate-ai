<template>
  <div class="flex flex-col items-center justify-center min-h-screen bg-gray-100">
    <div class="grid grid-cols-2 gap-4 w-3/4 mx-auto">
      <form @submit.prevent="translateText" class="p-6 bg-white rounded shadow">
        <h2 class="text-lg font-semibold mb-2">Введіть текст</h2>
        <textarea class="w-full h-32 p-2 border-none rounded bg-gray-100 mb-2"
                  placeholder="Введіть текст для перекладу" v-model="text"></textarea>
        <div class="flex items-center justify-between">
          <select name="location" v-model="lang"
                  class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
            <option value="en">Англійська</option>
            <option value="fr">Французька</option>
          </select>
          <button class="ml-2 px-4 py-2 bg-blue-500 text-white rounded" type="submit">Перекласти</button>
        </div>
      </form>
      <div class="p-6 bg-white rounded shadow">
        <h2 class="text-lg font-semibold mb-2">Переклад</h2>
        <div class="w-full h-32 p-2 border-none rounded bg-gray-100">{{translate}}</div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      text: 'привіт',
      lang: 'en',
      translate: '',
    };
  },
  methods: {
    async translateText() {
      try {
        const response = await this.$axios.$post('/translate', {
          text: this.text,
          lang: this.lang,
        });

        // Обробка відповіді
        this.translate = response.text
      } catch (error) {
        console.error(error);
      }
    },
  },
};
</script>
