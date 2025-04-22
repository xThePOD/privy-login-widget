import { usePrivy } from '@privy-io/react-auth';

function LoginComponent() {
  const { login, logout, authenticated, user, ready } = usePrivy();

  if (!ready) {
    return <div>Loading...</div>;
  }

  return (
    <div className="login-wrapper">
      {!authenticated ? (
        <button 
          className="privy-button" 
          onClick={login}
        >
          Log in with Privy
        </button>
      ) : (
        <div className="authenticated-container">
          <p className="user-info">
            Welcome, {user?.email || 'User'}!
          </p>
          <button 
            className="privy-button logout"
            onClick={logout}
          >
            Log out
          </button>
        </div>
      )}
    </div>
  );
}

export default LoginComponent; 