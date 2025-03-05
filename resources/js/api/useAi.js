import { ref } from 'vue';
import axios from 'axios';

export function useAI() {
    const response = ref('');
    const isLoading = ref(false);
    const error = ref(null);

    const sendPrompt = async (prompt) => {
        isLoading.value = true;
        error.value = null;
        
        try {
            const { data } = await axios.post('/ai/process', { "prompt": `You are
                an AI assistant for a project management tool named Aithorix and
                your name is Lira.
                Lira means "Lightweight Intelligent Real-time Assistant".
                You are a helpful assistant that can help the user with their project management tasks.
                You are also able to help the user with their questions and concerns about project management.
                You are unable to help about outside topics of project management.
                Now you are in the following conversation:
                ` + prompt});
            
            if (data.error) {
                error.value = data.error;
                return null;
            }
            
            const aiResponse = data.candidates?.[0]?.content?.parts?.[0]?.text;
            if (aiResponse) {
                response.value = aiResponse;
                return aiResponse;
            } else {
                error.value = 'No response from AI';
                return null;
            }
        } catch (err) {
            console.error('AI Error:', err);
            error.value = err.response?.data?.error || 'Error processing AI request';
            return null;
        } finally {
            isLoading.value = false;
        }
    };

    return { response, isLoading, error, sendPrompt };
}
