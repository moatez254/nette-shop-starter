# Pull Request

## 📋 Description
<!-- Provide a clear and concise description of what this PR accomplishes -->

## 🎯 Type of Change
<!-- Mark the appropriate option(s) with [x] -->

- [ ] 🐛 Bug fix (non-breaking change which fixes an issue)
- [ ] ✨ New feature (non-breaking change which adds functionality)
- [ ] 💥 Breaking change (fix or feature that would cause existing functionality to not work as expected)
- [ ] 📚 Documentation update
- [ ] 🧹 Code refactoring
- [ ] ⚡ Performance improvement
- [ ] 🧪 Test addition or improvement
- [ ] 🔧 CI/CD improvement
- [ ] 🐳 Docker/Infrastructure change

## 🔍 Related Issues
<!-- Link any related issues using #issue_number -->

Closes #(issue)
Related to #(issue)

## ✅ Checklist
<!-- Mark the appropriate option(s) with [x] -->

### Code Quality
- [ ] Code follows PSR-12 coding standards
- [ ] PHPStan passes with level max
- [ ] No new PHP warnings or errors
- [ ] Code is properly documented with PHPDoc
- [ ] Type hints are used where appropriate

### Testing
- [ ] Unit tests added/updated for new functionality
- [ ] Integration tests added/updated if applicable
- [ ] All tests pass locally
- [ ] Test coverage is maintained or improved
- [ ] Manual testing performed

### Documentation
- [ ] README.md updated if needed
- [ ] OpenAPI specification updated if API changes
- [ ] Code comments added for complex logic
- [ ] CHANGELOG.md updated (if applicable)

### Security
- [ ] No security vulnerabilities introduced
- [ ] Input validation implemented where needed
- [ ] SQL injection prevention maintained
- [ ] XSS protection maintained

### Performance
- [ ] No performance regressions introduced
- [ ] Database queries are optimized
- [ ] Memory usage is reasonable

## 🧪 Testing Instructions
<!-- Provide step-by-step instructions for testing this PR -->

1. **Setup**: 
   ```bash
   git checkout feature/your-feature-branch
   composer install
   ```

2. **Database**: 
   ```bash
   # Create test database
   sqlite3 var/database.sqlite < db/schema.sql
   sqlite3 var/database.sqlite < db/seeds.sql
   ```

3. **Run Tests**: 
   ```bash
   composer test
   composer stan
   composer lint
   ```

4. **Manual Testing**: 
   - [ ] Test the new feature/functionality
   - [ ] Verify existing functionality still works
   - [ ] Check error handling scenarios

## 📸 Screenshots
<!-- If applicable, add screenshots to help explain your changes -->

## 🔄 Breaking Changes
<!-- If this PR includes breaking changes, describe them here -->

## 📊 Performance Impact
<!-- If applicable, describe any performance implications -->

## 🔒 Security Considerations
<!-- If applicable, describe any security implications -->

## 📝 Additional Notes
<!-- Add any other context about the pull request here -->

## 🚀 Deployment Notes
<!-- Any special considerations for deployment -->

---

**By submitting this pull request, I confirm that:**
- [ ] I have read and understood the contributing guidelines
- [ ] My code follows the project's coding standards
- [ ] I have tested my changes thoroughly
- [ ] I have updated all relevant documentation
- [ ] My changes do not introduce any new security vulnerabilities 