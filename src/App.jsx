import { PrivyProvider } from '@privy-io/react-auth';
import LoginComponent from './components/LoginComponent';
import './styles/Login.css';

function App() {
  return (
    <PrivyProvider
      appId="cm9ja1fsw00mpie0na7ikxm7a"
      config={{
        loginMethods: ['email', 'wallet'],
        appearance: {
          theme: 'dark',
          accentColor: '#0f62fe',
          showWalletLoginFirst: false,
        },
        embeddedWallets: {
          createOnLogin: 'all-users',
          noPromptOnSignature: false,
        },
      }}
    >
      <LoginComponent />
    </PrivyProvider>
  );
}

export default App; 