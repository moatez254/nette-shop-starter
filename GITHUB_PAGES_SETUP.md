# GitHub Pages Setup Guide

## 🚀 Enable GitHub Pages for API Documentation

To enable the automatic deployment of API documentation to GitHub Pages, follow these steps:

### Step 1: Enable GitHub Pages in Repository Settings

1. Go to your repository: https://github.com/moatez254/nette-shop-starter
2. Click on **Settings** tab
3. Scroll down to **Pages** section in the left sidebar
4. Under **Source**, select **GitHub Actions**
5. Click **Save**

### Step 2: Verify Permissions (if needed)

If you encounter permission issues:

1. Go to **Settings** → **Actions** → **General**
2. Under **Workflow permissions**, select:
   - ✅ **Read and write permissions**
   - ✅ **Allow GitHub Actions to create and approve pull requests**
3. Click **Save**

### Step 3: Trigger the Workflow

The API documentation will be automatically built and deployed when:
- You push changes to the `main` branch
- You modify `openapi.yaml` file
- You update the workflow file

### Step 4: Access Your Documentation

Once the workflow completes successfully:

- **API Documentation**: https://moatez254.github.io/nette-shop-starter/
- **OpenAPI Spec**: https://moatez254.github.io/nette-shop-starter/openapi.yaml

### 🔧 Troubleshooting

#### Permission Denied Error (403)
If you see this error, ensure:
1. GitHub Pages is enabled (Step 1)
2. Workflow permissions are set correctly (Step 2)
3. Repository is public or you have GitHub Pro/Enterprise

#### Workflow Not Triggering
1. Check that the workflow file exists in `.github/workflows/api-docs.yml`
2. Verify the `on` trigger conditions are met
3. Check the **Actions** tab for any error messages

#### Build Fails
1. Check that `openapi.yaml` is valid:
   ```bash
   # Install Redocly CLI locally
   npm i -g @redocly/cli
   
   # Validate your OpenAPI spec
   redocly lint openapi.yaml
   ```

### 📚 What Gets Deployed

The workflow creates a beautiful documentation site with:
- **Interactive API Documentation** (ReDoc)
- **OpenAPI Specification** (downloadable)
- **Quick Start Examples**
- **API Reference**

### 🎯 Manual Deployment (Alternative)

If GitHub Pages isn't available, you can build locally:

```bash
# Install Redocly CLI
npm i -g @redocly/cli

# Build documentation
mkdir -p docs
redocly build-docs openapi.yaml -o docs/index.html

# Serve locally
cd docs && python -m http.server 8080
# Visit: http://localhost:8080
```

---

## 🌟 Success!

Once setup is complete, your API documentation will be automatically updated and deployed every time you push changes to your OpenAPI specification. This provides a professional, always-up-to-date API reference for your users and stakeholders. 