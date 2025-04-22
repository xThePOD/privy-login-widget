import { usePrivy } from '@privy-io/react-auth';

function LoginComponent() {
  const { login, logout, authenticated, user, ready } = usePrivy();

  if (!ready) {
    return (
      <div className="login-wrapper">
        <div className="loading-spinner">Loading...</div>
      </div>
    );
  }

  return (
    <div className="login-wrapper">
      {!authenticated ? (
        <div className="login-container">
          <h1 className="login-title">Welcome to Our App</h1>
          <p className="login-subtitle">Please log in to continue</p>
          <button 
            className="privy-button" 
            onClick={login}
          >
            Log in with Privy
          </button>
        </div>
      ) : (
        <div className="authenticated-container">
          <div className="user-profile">
            <h2 className="user-info">
              Welcome, {user?.email || 'User'}!
            </h2>
            {user?.email && (
              <p className="user-email">{user.email}</p>
            )}
          </div>
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