# 🔐 GitHub SSH Setup - Quick Guide

## Your SSH Public Key:

```
ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAINHBmfT7ZFXWDNIgksZqbSNyxiGf6Seoi3ytiIiDgGBc rajanihadi17@gmail.com
```

---

## 📋 STEP-BY-STEP INSTRUCTIONS:

### **Step 1: Add SSH Key to GitHub**

1. **Open this URL in your browser:**
   ```
   https://github.com/settings/ssh/new
   ```

2. **Fill in the form:**
   - **Title:** `TanBooking Server (Windows PC)`
   - **Key type:** Select `Authentication Key`
   - **Key:** Copy and paste the entire line above (starting with `ssh-ed25519`)

3. **Click "Add SSH key"**

4. **Enter your GitHub password if prompted**

---

### **Step 2: Test SSH Connection**

Open a **NEW** command prompt and run:

```cmd
ssh -T git@github.com
```

You should see:
```
Hi mmehdirajani05! You've successfully authenticated, but GitHub does not provide shell access.
```

If you see "Permission denied", run these commands first:

```cmd
start-ssh-agent
ssh-add C:\Users\HP\.ssh\id_ed25519
```

Then test again:
```cmd
ssh -T git@github.com
```

---

### **Step 3: Push to GitHub**

Once SSH is working, push your changes:

```cmd
cd C:\Hadi\Projects\tanbooking-server-app
git push origin main
```

---

## ✅ VERIFICATION

After successful push, verify at:
```
https://github.com/mmehdirajani05/tanbooking-server-app
```

---

## 🔄 ALTERNATIVE: Use GitHub Desktop

If SSH continues to be problematic, you can use GitHub Desktop:

1. **Download:** https://desktop.github.com/
2. **Install and login** with your GitHub account
3. **Add local repository:** File → Add Local Repository → Select `C:\Hadi\Projects\tanbooking-server-app`
4. **Push:** Click "Push origin"

---

## 📞 NEED HELP?

If you're stuck:
1. Make sure you added the SSH key to GitHub (Step 1)
2. Make sure SSH agent is running
3. Try testing SSH connection (Step 2)
4. Then push (Step 3)

---

**The remote is already switched to SSH, so once you add the key to GitHub, everything will work automatically!** 🚀
