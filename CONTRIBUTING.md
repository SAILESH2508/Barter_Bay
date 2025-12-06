# Contributing to Barter Bay

First off, thank you for considering contributing to Barter Bay! 🎉

## Code of Conduct

This project and everyone participating in it is governed by our Code of Conduct. By participating, you are expected to uphold this code.

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check the existing issues to avoid duplicates. When you create a bug report, include as many details as possible:

- **Use a clear and descriptive title**
- **Describe the exact steps to reproduce the problem**
- **Provide specific examples**
- **Describe the behavior you observed and what you expected**
- **Include screenshots if possible**
- **Include your environment details** (PHP version, OS, browser)

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion, include:

- **Use a clear and descriptive title**
- **Provide a detailed description of the suggested enhancement**
- **Explain why this enhancement would be useful**
- **List any similar features in other applications**

### Pull Requests

1. Fork the repo and create your branch from `main`
2. If you've added code that should be tested, add tests
3. Ensure your code follows the existing style
4. Make sure your code lints
5. Issue that pull request!

## Development Setup

1. Clone your fork
```bash
git clone https://github.com/your-username/barter-bay.git
cd barter-bay
```

2. Create a branch
```bash
git checkout -b feature/your-feature-name
```

3. Make your changes and commit
```bash
git add .
git commit -m "Add some feature"
```

4. Push to your fork
```bash
git push origin feature/your-feature-name
```

5. Open a Pull Request

## Coding Standards

### PHP
- Follow PSR-12 coding standards
- Use type hints where possible
- Document functions with PHPDoc
- Use meaningful variable names

### Security
- **Always use prepared statements** for database queries
- **Escape all user outputs** with `htmlspecialchars()`
- **Add CSRF tokens** to all forms
- **Validate all inputs** on the server side
- **Never expose sensitive data** in error messages

### Database
- Use prepared statements with parameter binding
- Always use transactions for multi-step operations
- Index frequently queried columns

### JavaScript
- Use modern ES6+ syntax
- Add comments for complex logic
- Validate inputs on client side (but always validate server-side too)

### CSS
- Use meaningful class names
- Keep styles organized
- Ensure responsive design

## Testing

Before submitting a PR, test:
- [ ] User registration and login
- [ ] Product browsing and search
- [ ] Cart functionality
- [ ] Checkout process
- [ ] Trading system
- [ ] Admin panel
- [ ] Mobile responsiveness

## Commit Messages

- Use the present tense ("Add feature" not "Added feature")
- Use the imperative mood ("Move cursor to..." not "Moves cursor to...")
- Limit the first line to 72 characters
- Reference issues and pull requests after the first line

Examples:
```
Add user profile editing feature

- Add profile.php page
- Add form validation
- Update database schema
Fixes #123
```

## Documentation

- Update README.md if you change functionality
- Add comments for complex code
- Update API documentation if applicable

## Questions?

Feel free to open an issue with your question or contact the maintainers.

Thank you for contributing! 🙏
