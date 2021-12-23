## Update docker image in DockerHub https://hub.docker.com/r/abantecart/abantecart

### 1. Build Docker image 
```#  docker build -t abantecart .```

### 2. Tag Docker image
```# docker tag abantecart:latest abantecart/abantecart:latest```

### 3. Login Docker Hub
```# docker login --username .......```

### 4. Push Docker image to AWS ECR
```# docker push abantecart/abantecart:latest```