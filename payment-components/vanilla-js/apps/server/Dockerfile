FROM node:18

WORKDIR /server

COPY package.json package-lock.json ./

RUN npm install

EXPOSE 8082

CMD ["npm", "run", "dev"]

