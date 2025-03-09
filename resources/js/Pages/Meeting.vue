<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { ZegoExpressEngine } from "zego-express-engine-webrtc";
import axios from "axios";

// Get the app ID from environment variables
const appID = parseInt(import.meta.env.VITE_ZEGO_APP_ID);
const roomID = "aithorix-meeting";
const userID = "User_" + Math.floor(Math.random() * 10000);
const userName = "User_" + Math.floor(Math.random() * 10000);

const localStream = ref(null);
const remoteStreams = ref(new Map());
const isPublishing = ref(false);
const error = ref(null);
const zg = ref(null);

const checkBrowserCompatibility = () => {
  const ua = navigator.userAgent;
  const browserInfo = {
    isChrome: /Chrome/.test(ua) && !/Edge/.test(ua) && !/Edg/.test(ua),
    isFirefox: /Firefox/.test(ua),
    isSafari: /Safari/.test(ua) && !/Chrome/.test(ua),
    isEdge: /Edge/.test(ua) || /Edg/.test(ua),
    version: ''
  };

  // Get browser version
  let match;
  if (browserInfo.isEdge) {
    match = ua.match(/(Edge|Edg)\/(\d+)/);
  } else if (browserInfo.isChrome) {
    match = ua.match(/Chrome\/(\d+)/);
  } else if (browserInfo.isFirefox) {
    match = ua.match(/Firefox\/(\d+)/);
  } else if (browserInfo.isSafari) {
    match = ua.match(/Version\/(\d+)/);
  }

  if (match) {
    browserInfo.version = parseInt(match[match.length - 1]);
  }

  return browserInfo;
};

const initializeZego = async () => {
  try {
    console.log('Checking browser compatibility...');
    const browser = checkBrowserCompatibility();
    console.log('Browser info:', browser);

    // Check if browser is supported
    if (!browser.isChrome && !browser.isFirefox && !browser.isEdge && !browser.isSafari) {
      throw new Error('Your browser is not supported. Please use Chrome, Firefox, Edge, or Safari.');
    }

    // Check minimum versions
    if (
      (browser.isChrome && browser.version < 70) ||
      (browser.isFirefox && browser.version < 65) ||
      (browser.isEdge && browser.version < 80) ||
      (browser.isSafari && browser.version < 12)
    ) {
      throw new Error('Please update your browser to the latest version.');
    }

    console.log('Initializing Zego with appID:', appID);
    
    // Create ZegoExpressEngine instance
    zg.value = new ZegoExpressEngine(appID, 'wss://webliveroom' + appID + '-api.zego.im/ws');
    
    // Check environment
    const result = await zg.value.checkSystemRequirements();
    console.log('System requirements check:', result);
    
    if (!result.webRTC) {
      throw new Error('WebRTC is not supported in this browser. Please make sure you\'re using a modern browser and have allowed camera and microphone access.');
    }

    // Check for SSL in production
    if (window.location.protocol !== 'https:' && window.location.hostname !== 'localhost') {
      throw new Error('Video calls require a secure connection (HTTPS) unless you\'re on localhost.');
    }

    // Add event listeners
    zg.value.on('roomStreamUpdate', async (roomID, updateType, streamList, extendedData) => {
      console.log('roomStreamUpdate', roomID, updateType, streamList, extendedData);
      if (updateType === 'ADD') {
        for (const stream of streamList) {
          try {
            const remoteStream = await zg.value.startPlayingStream(stream.streamID);
            remoteStreams.value.set(stream.streamID, remoteStream);
            
            const videoElement = document.createElement('video');
            videoElement.id = `remote-${stream.streamID}`;
            videoElement.autoplay = true;
            videoElement.playsInline = true;
            videoElement.srcObject = remoteStream;
            document.getElementById('remote-streams')?.appendChild(videoElement);
          } catch (err) {
            console.error('Failed to play remote stream:', err);
          }
        }
      } else if (updateType === 'DELETE') {
        for (const stream of streamList) {
          const videoElement = document.getElementById(`remote-${stream.streamID}`);
          if (videoElement) {
            videoElement.remove();
          }
          remoteStreams.value.delete(stream.streamID);
        }
      }
    });

    zg.value.on('roomStateUpdate', (roomID, state, errorCode, extendedData) => {
      console.log('Room state update:', { roomID, state, errorCode, extendedData });
      if (state === 'DISCONNECTED') {
        error.value = `Room disconnected (${errorCode}): ${extendedData?.message || 'Connection lost'}`;
      }
    });

    zg.value.on('publisherStateUpdate', (result) => {
      console.log('Publisher state update:', result);
      if (result.state === 'NO_PUBLISH') {
        error.value = 'Failed to publish stream: ' + result.errorCode;
      }
    });

    zg.value.on('playerStateUpdate', (result) => {
      console.log('Player state update:', result);
    });

  } catch (err) {
    error.value = err.message;
    console.error('Initialization error:', err);
    return false;
  }
  return true;
};

const getToken = async () => {
  try {
    console.log('Getting token for user:', userID);
    const response = await axios.post("/zego-token", { 
      user_id: userID,
      room_id: roomID 
    });
    console.log('Token response:', response.data);
    return response.data.token;
  } catch (err) {
    error.value = 'Failed to get token: ' + (err.response?.data?.error || err.message);
    console.error('Failed to get token:', err);
    return null;
  }
};

const startLocalStream = async () => {
  try {
    console.log('Starting local stream...');
    
    // Request permissions first
    try {
      await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
    } catch (err) {
      if (err.name === 'NotAllowedError') {
        throw new Error('Please allow camera and microphone access to join the meeting.');
      } else if (err.name === 'NotFoundError') {
        throw new Error('No camera or microphone found. Please connect a device and try again.');
      } else {
        throw err;
      }
    }
    
    const constraints = {
      video: true,
      audio: true
    };
    
    localStream.value = await zg.value.createStream(constraints);
    const localVideo = document.getElementById('local-video');
    if (localVideo) {
      localVideo.srcObject = localStream.value;
    }
    
    // Start publishing
    console.log('Publishing stream...');
    const streamID = `${userID}_${Date.now()}`;
    await zg.value.startPublishingStream(streamID, localStream.value);
    isPublishing.value = true;
  } catch (err) {
    error.value = err.message;
    console.error('Failed to start local stream:', err);
  }
};

const joinRoom = async () => {
  try {
    console.log('Getting token...');
    const token = await getToken();
    if (!token) {
      throw new Error('Failed to get token');
    }

    console.log('Joining room:', roomID);
    const result = await zg.value.loginRoom(roomID, token, {
      userID,
      userName
    });

    console.log('Room join result:', result);
    if (result) {
      await startLocalStream();
  } else {
      throw new Error('Failed to join room');
    }
  } catch (err) {
    error.value = 'Failed to join room: ' + err.message;
    console.error('Failed to join room:', err);
  }
};

const leaveRoom = async () => {
  try {
    if (isPublishing.value) {
      await zg.value.stopPublishingStream();
      isPublishing.value = false;
    }
    
    // Stop all remote streams
    for (const [streamID, stream] of remoteStreams.value) {
      await zg.value.stopPlayingStream(streamID);
    }
    remoteStreams.value.clear();
    
    // Stop local stream
    if (localStream.value) {
      localStream.value.getTracks().forEach(track => track.stop());
      localStream.value = null;
    }
    
    await zg.value.logoutRoom(roomID);
  } catch (err) {
    console.error('Error leaving room:', err);
  }
};

onMounted(async () => {
  console.log('Component mounted');
  await initializeZego();
  await joinRoom();
});

onUnmounted(async () => {
  console.log('Component unmounting');
  await leaveRoom();
  if (zg.value) {
    zg.value.destroyEngine();
  }
});
</script>

<template>
  <Head title="Video Meeting" />
  
  <div class="min-h-screen bg-gray-100 p-6">
    <div class="max-w-7xl mx-auto">
      <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-6">Video Meeting</h1>
        
        <!-- Error Alert -->
        <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          {{ error }}
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Local Stream -->
          <div class="relative">
            <h2 class="text-lg font-semibold mb-2">Your Video</h2>
            <video
              id="local-video"
              class="w-full aspect-video bg-gray-900 rounded-lg"
              autoplay
              playsInline
              muted
            ></video>
          </div>

          <!-- Remote Streams -->
          <div class="relative">
            <h2 class="text-lg font-semibold mb-2">Remote Videos</h2>
            <div 
              id="remote-streams"
              class="grid grid-cols-2 gap-2"
            >
              <!-- Remote videos will be dynamically added here -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
video {
  background-color: #1a1a1a;
  border-radius: 0.5rem;
}

#remote-streams video {
  width: 100%;
  aspect-ratio: 16/9;
  object-fit: cover;
}
</style>
